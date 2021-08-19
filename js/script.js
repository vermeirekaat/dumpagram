{
    // formtodataJson is een functie die de name - value pairs v/e formulier omzet naar JSON formaat 
    const formdataToJson = $from => {
        const data = new FormData($from);
        const obj = {}
        data.forEach((value, key) => {
            console.log(key + ' : ' + value);
            obj[key] = value;
        });
        return obj;
    }

    // DETAIL - COMMENTS 
    const handleSubmitComments = e => {
        const $form = e.currentTarget; 
        e.preventDefault(); 
        // gegevens van de form ophalen en deze omzetten naar een json bestand 
        postComment($form.getAttribute('action'), formdataToJson($comments));
    }
    // functie die het formulier zal afhandelen 
    const postComment = async (url, data) => {
        // versturen naar de server met de juiste route, header geeft aan dat de respons iets zal zijn in json vormaat 
        // body bevat de data van het form 
        const response = await fetch(url, {
            method: "POST",
            headers: new Headers({
                'Content-Type': 'application/json'
            }),
            body: JSON.stringify(data)
        });
        // vorig onderdeel is doorgestuurd naar PHP (request naar de server) en u handelt JS de rest af
        const returned = await response.json();
        console.log(returned);
        if (returned.error) {
            console.log(response.error);
        } else {
            showComments(returned);
        }
    }; 
    // comments opbouwen om deze te tonen 
    const showComments = comments => {
        const $list = document.querySelector(`.form__comment`); 
        $list.innerHTML = ''; 
        comments.forEach(comment => {
            const $listItem = document.createElement(`li`); 
            $listItem.innerHTML = `class="comments__item" ${comment.comment}`; 
            $list.appendChild($listItem);
        })
    }

    // DETAIL - REACTIONS 
    const handleSubmitReactions = e => {
        $reactions = e.currentTarget; 
        e.preventDefault(); 
        postReaction($reactions.getAttribute('action'), formdataToJson($reactions));
    }
    postReaction = async (url, data) => {
        const response = await fetch(url, {
            method: "POST",
            headers: new Headers({
                'Content-Type': 'application/json'
            }),
            body: JSON.stringify(data)
        });
        const returned = await response.json(); 
        console.log(returned);
        if (returned.error) {
            console.log(returned.error); 
        } else {
            return returned;
        }
    }
    
    // FILTER
    const handleSubmitForm = e => {
        e.preventDefault();
        submitWithJS();
    };

    const handleInputField = e => {
        submitWithJS();
    }; 

    const submitWithJS = async () => {
        // data van het formulier ophalen en resultaten checken in de console 
        const $form = document.querySelector(`.search-form`); 
        const data = new FormData($form); 
        const entries = [...data.entries()]; 
        console.log('entries', entries); 
        const qs = new URLSearchParams(entries).toString(); 
        console.log('querystring', qs); 
        const url = `${$form.getAttribute('action')}?page=search&${qs}`; 
        console.log('url', url); 

        // request naar de server 
        const response = await fetch(url, {
            headers: new Headers({
                Accept: 'application/json'
            })
        });
        // opslaan van de images/ results die de server heeft teruggegeven 
        const results = await response.json();
        updateList(results); 

        // aanpassen van de url en beschikbaar maken van de back button 
        window.history.pushState(
            {},
            '',
            `${window.location.href.split('?')[0]}?${qs}`
        ); 
    }

    const updateList = results => {
        const $results = document.querySelector(`.gallery`);
        // elementen aanmaken via JAvaScript i.p.v. via PHP 
        $results.innerHTML = results.map(result => {
            return `
            <li class="image-grid__item">
            <a class="link" href="index.php?page:detail&id=${result.id}>
            <div class="image-placement>
            <img class="image__overview" src="${result.path}" alt="${result.title}">
            </div>
            <p class="image__text"><span>&#9872;</span></p>
            <p class="image__text">${result.title}</p>
            </a>
            </li>
            `;
        }).join(``);
    };

    const init = () => {
        // DETAIL - COMMENTS 
        const $commentForm = document.querySelector(`.comment-form`); 
        // checken of dit form bestaat op de pagina 
        if ($commentForm) {
            $commentForm.addEventListener(`submit`, handleSubmitComments); 
        }

        // DETAIL - REACTIONS 
        const $reactionForm = document.querySelector(`.reaction-form`); 
        if ($reactionForm) {
            $reactionForm.addEventListener(`submit`, handleSubmitReactions); 
        }

        // FILTER
        // class toevoegen om te detecteren of JavaScript beschikbaar is of niet 
        document.documentElement.classList.add(`has-js`); 
        document.querySelectorAll(`.search__input`).forEach($input => $input.addEventListener(`input`, handleInputField));
        $searchForm = document.querySelector('.search-form'); 
        if ($searchForm) {
            $searchForm.addEventListener(`submit`, handleSubmitForm); 
        } 
    }

    init(); 
}