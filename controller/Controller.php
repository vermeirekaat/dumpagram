<?php

class Controller {

  public $route;
  protected $viewVars = array();

  public function filter() {
    call_user_func(array($this, $this->route['action']));
  }

  public function render() {
    $this->createViewVarWithContent();
    $this->renderInLayout();
  }

  public function set($variableName, $value) {
    $this->viewVars[$variableName] = $value;
  }

  private function createViewVarWithContent() {
    extract($this->viewVars, EXTR_OVERWRITE);
    ob_start();
    require __DIR__ . '/../view/' . strtolower($this->route['controller']) . '/' . $this->route['action'] . '.php';
    $content = ob_get_clean();
    $this->set('content', $content);
  }

  private function renderInLayout() {
    extract($this->viewVars, EXTR_OVERWRITE);
    include __DIR__ . '/../view/layout.php';

    // na het renderen van de view zal unset() ervoor zorgen dat het ene element uit de session verwijderd wordt na het herladen van de pagina 
    if(!empty($_SESSION['info'])) {
      unset($_SESSION['info']); 
    }
    if(!empty($_SESSION['error'])) {
      unset($_SESSION['error']); 
    }
  }

}
