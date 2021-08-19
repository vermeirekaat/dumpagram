<?php 

require_once __DIR__ . '/Controller.php'; 
require_once __DIR__ . '/../dao/CommentsDAO.php'; 
require_once __DIR__ . '/../dao/ImagesDAO.php'; 
require_once __DIR__ . '/../dao/ImagesReactionsDAO.php'; 
require_once __DIR__ . '/../dao/ReactionsDAO.php'; 

class PagesController extends Controller {

    private $commentsDAO;
    private $imagesDAO; 
    private $imagesReactionsDAO; 
    private $reactionsDAO; 

    function __construct() {
        $this->commentsDAO = new CommentsDAO(); 
        $this->imagesDAO = new ImagesDAO(); 
        $this->imagesReactionsDAO = new ImagesReactionsDAO(); 
        $this->reactionsDAO = new ReactionsDAO(); 
    }

    public function index() {
        $imagesRecent = $this->imagesDAO->selectRecentImages(); 
        $this->set('imagesRecent', $imagesRecent); 

        // reactions tellen 
        foreach ($imagesRecent as $index => $imageRecent) {
            $imagesRecent[$index]['amount'] = $this->imagesReactionsDAO->countReactionByImage($imageRecent['id'])['amount'];
        }

        $this->set('imagesRecent', $imagesRecent);
        $this->set('title', 'Recent'); 
    }

    public function popular() {

        // populairste foto's weergeven
        $imagesPopular = $this->imagesDAO->orderPopularImages(); 
        $this->set('imagesPopular', $imagesPopular);
        $this->set('title', 'Popular');
        
    }
    public function detail() {
        if(!empty($_GET['id'])) {
            // selectImage = de juiste foto ophalen o.b.v. het id 
            $image = $this->commentsDAO->selectImage($_GET['id']); 
            $this->set('image', $image);
        }

        // POST request komt binnen vanuit JavaScript 
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $data = json_decode($content, true); 

            $insertedComment = $this->commentsDAO->insertComment($data); 
            if(!$insertedComment) {
                $errors = $this->commentsDAO->validate($data); 
                $errors['error'] = "Something isn't working well"; 
                echo json_encode($errors);
            } else {
                $comments = $this->commentsDAO->selectByImageId($data['image_id']); 
                echo json_encode($comments); 
            }
            exit(); 
        }

        // selectByImageId = de juiste comments ophalen van de image o.b.v. image_id
        $imageComments = $this->commentsDAO->selectByImageId($image['id']); 
        $this->set('imageComments', $imageComments); 

        // insertComment = comment toevoegen aan een foto 
        // checken of er een post is en of dit een insertComment is 
        if(!empty($_POST['action'])) {
            if ($_POST['action'] == 'insertComment') {
                // array met data invullen 
                $data = array(
                    // elk comment krijgt zelf een comment bij het versturen v/d form dus deze moet niet opgehaald worden 
                    'image_id' => $image['id'], 
                    'comment' => $_POST['comment']
                );
                // aanspreken van de dao 
                $insertedComment = $this->commentsDAO->insertComment($data); 
                // controleren of alles foutloos is verlopen 
                if (!$insertedComment) {
                    $errors = $this->commentsDAO->validate($data); 
                    $this->set('errors', $errors);
                } else {
                    header('Location: index.php?page=detail&id='.$image['id']);
                    exit(); 
                }
            }
        }

        // tabel reactions ophalen uit de database halen om deze toe te voegen in de view (form)
        $reactions = $this->reactionsDAO->selectReactions(); 
        $this->set('reactions', $reactions); 

        // reacties tellen per foto
        foreach ($reactions as $index => $reaction) {
            $reactions[$index]['reaction_amount'] = $this->imagesReactionsDAO->countAmazedByImage($image['id'], $reaction['id'])['reaction_amount']; 
        }
        $this->set('reactions', $reactions); 

        // request binnengekregen van JavaScript, PHP stuurt bepaalde gegevens door naar de DAO en stuurt deze terug in JSON formaat naar JS die dit verder afwerkt 
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : ''; 
        if($contentType === "application/json") {
            $content = trim(file_get_contents("php://input")); 
            $data = json_decode($content, true); 

            $insertedReaction = $this->imagesReactionsDAO->insertReaction($data); 
            if(!$insertedReaction) {
                $errors = $this->imagesReactionsDAO->validate($data); 
                $errors['error'] = "Something went wrong"; 
                echo json_encode($errors);
            }
            exit();
        }

        // checken op welke button er is geklikt (id nagaan van de reaction)
        if(!empty($_POST['action'])) {
            if ($_POST['action'] == 'insertReaction') {
                // array met data aanmaken 
                $data = array(
                    'image_id' => $image['id'],
                    'reaction_id' => $_POST['reaction_id']
                );
                // aanspreken van de DAO 
                $selectedReaction = $this->imagesReactionsDAO->insertReaction($data); 
                // controlerren of alles foutloos is verlopen 
                if (!$selectedReaction) {
                    $errors = $this->imagesReactionsDAO->validate($data); 
                    $this->set('errors', $errors); 
                } else {
                    header('Location: index.php?page=detail&id='.$image['id']); 
                    exit(); 
                }
            }
        }
        $this->set('title', 'Popular');
        // deze lijn code zorgt ervoor dat de amount automatisch wordt aangepast bij het herladen van de pagina wanneer de gebruiker op een reactie klikt
        $this->imagesReactionsDAO->countAmazedByImage($image['id'], $reaction['id']);
    }

    public function upload() {
        
        // variabele om foutmelding bij te houden
        $error = '';

        // controleer of er iets in de $_POST zit
        if (!empty($_POST['action'])) {
            // controleer of het wel om het juiste formulier gaat
            if ($_POST['action'] == 'addImage') {
                // controleren of er een bestand werd geselecteerd
                if (empty($_FILES['image']) || !empty($_FILES['image']['error'])) {
                    $error = 'Please select a file';
                }

                if (empty($error)) {
                    // controleer of het een afbeelding is van het type jpg, png of gif
                    $whitelist_type = array('image/jpeg', 'image/png', 'image/gif');
                    if (!in_array($_FILES['image']['type'], $whitelist_type)) {
                        $error = 'Please select the right file';
                    }
                }

                if (empty($error)) {
                    // controleer de afmetingen van het bestand: pas deze gerust aan voor je eigen project
                    // width: 270
                    // height: 480
                    $size = getimagesize($_FILES['image']['tmp_name']);
                    if ($size[0] < 350 || $size[1] < 350) {
                        $error = 'The image should have the following dimensions 350 x 350';
                    }
                }

                if (empty($error)) {
                    // map met een random naam aanmaken voor de upload: redelijk zeker dat er geen conflict is met andere uploads
                    $projectFolder = realpath(__DIR__);
                    $targetFolder = $projectFolder . '/../assets/uploads';
                    $targetFolder = tempnam($targetFolder, '');
                    unlink($targetFolder);
                    mkdir($targetFolder, 0777, true);
                    $targetFileName = $targetFolder . '/' . $_FILES['image']['name'];

                    // via de functie _resizeAndCrop() de afbeelding croppen en resizen tot de gevraagde afmeting
                    $this->_resizeAndCrop($_FILES['image']['tmp_name'], $targetFileName, 270, 480);
                    $relativeFileName = substr($targetFileName, strlen($projectFolder) - strlen('controller'));

                    $data = array(
                        'image' => $relativeFileName,
                        'title' => $_POST['title'],
                        'description' => $_POST['description']
                    );

                    // TODO: schrijf de afbeelding weg naar de database o.b.v. de array $data 
                    $uploadedImage = $this->imagesDAO->uploadImage($data);
                    // controlleren of dit is gelukt 
                    if (!$uploadedImage) {
                        // TODO: zorg dat de variabele $error getoond wordt indien er iets fout gegaan is
                        $errors = $this->imagesDAO->validate($data);
                        $this->set('errors', $errors);
                    } else {
                        // als het gelukt is om de image up te loaden zal de boodschap van de session getoond worden wanneer de pagina een redirect doet naar index.php
                        $_SESSION['info'] = '&check; Your image has been added to the gallery &check;'; 
                        header('Location: index.php');
                        exit(); 
                    }
                }
            }
            $this->set('title', 'Upload');
        }
    }

    private function _resizeAndCrop($src, $dst, $thumb_width, $thumb_height)
    {
        $type = exif_imagetype($src);
        $allowedTypes = array(
            1,  // [] gif
            2,  // [] jpg
            3  // [] png
        );

        if (!in_array($type, $allowedTypes)) {
            return false;
        }

        switch ($type) {
            case 1:
                $image = imagecreatefromgif($src);
                break;
            case 2:
                $image = imagecreatefromjpeg($src);
                break;
            case 3:
                $image = imagecreatefrompng($src);
                break;
            case 6:
                $image = imagecreatefrombmp($src);
                break;
        }

        $filename = $dst;

        $width = imagesx($image);
        $height = imagesy($image);

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        // Resize and crop
        imagecopyresampled(
            $thumb,
            $image,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );
        imagejpeg($thumb, $filename, 80);
        return true;
    }

    public function search() {
        // elk criteria krijgt een default waarde en wordt ingevuld indien opgegeven 
        $title = '';
        if (!empty($_GET['title'])) {
            $title = $_GET['title'];
        }
        $description = '';
        if (!empty($_GET['description'])) {
            $description = $_GET['description'];
        }

        // parameters doorgeven naar de DAO en de images/ results ophalen 
        $results = $this->imagesDAO->selectImagesWithFilters($title, $description);
        // indien er een request via JavaScript kwam worden de resultaten als JSON teruggegeven 
        if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
            echo json_encode($results);
            exit();
        }
        // resultaten doorgeven naar de view indien geen Javascript 
        $this->set('results', $results); 
        $this->set('title', 'Search');
    }
}