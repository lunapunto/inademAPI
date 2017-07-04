<?php

class user_db extends db{
  public $id;
  public $obj;
  public $email;
  public $phone;
  public $picture;
  public $cssbg;
  public function __construct($value = false,$key = 'ID'){
    parent::__construct();
    if($value){
      $this->get($value,$key);
    }else{
      $this->id = 0;
    }
  }
  public function get($value, $key = 'ID' , $attach = true){
    $user = $this->fetch('SELECT * FROM users WHERE '.$key.'="'.$value.'"');
    if(count($user)){
      if($attach){
        $this->id = $user['ID'];
        $this->obj = (object) $user;
        $this->email = $user['email'];
        $this->phone = $user['phone'];
        $this->picture = $user['picture'];
        $this->cssbg = ($this->picture ? 'url('.$this->picture.')' : false);
      }
      return true;
    }else{
      return false;
    }
  }
  public function create($info = array(), $attachcart = false){
    $info['SCS']  = rand(100000,999999);
    if($attachcart){
      $cvs['cart'] = $attachart;
    }
    $id = $this->i('users',$info);
    return $id;
  }
  public function update($cvs,$id = false){
    if(!$id){
      $id = $this->id;
    }
    $this->u('users',$cvs,array('ID'=>$id));
  }
  public function delete($value, $field = 'email'){
    $this->d('users', array($field=>$value));
  }
}
class current_user{
  public $db;
  public $info;
  public $email;
  public $name;
  public $role;
  public $isguest = true;
  public $error = false;
  public function __construct($id = false){
    $this->db = new user_db;
    $db = $this->db;
    if(!$id){
      $this->isguest = !$this->isloggedin();
    }else{
      $this->isguest = false;
      $user = $db->get($id);
    }

  }
  public function isloggedin(){
    $id = isset($_COOKIE[SLUG.'_id']) ? $_COOKIE[SLUG.'_id'] : false;
    $scs = isset($_COOKIE[SLUG.'_scs']) ? $_COOKIE[SLUG.'_scs'] : false;
    $db = $this->db;
    if($id){
      $user = $db->get($id);
      $email = $db->email;
      $isok = hash_equals($scs, crypt($email, $db->obj->SCS));
      if($isok){
        $this->info = $db->obj;
        $this->email = $db->email;
        $this->name = $db->obj->name;
        return true;
      }
    }else{
      $this->error = 'No existen cookies de usuario.';
      return false;
    }
  }
  public function redirect($screen = 'guestFilter'){
    $redir = false;
    $islogged = !$this->isguest;
    switch($screen){
      case "guestFilter":
        if($islogged){
          $redir = 'home';
        }else{
          $redir = 'login';
        }
      break;
      case "home":
        if(!$islogged){
          $redir = 'login';
        }
      break;
      case "login":
        if($islogged){
          $redir = 'home';
        }
      break;
    }
    if($redir){
      if($redir == 'home' && $this->db->role == 'portier'){
        $redir = 'portierHome';
      }
      header('Location:'.dir.'/'.$redir);
      die();
    }
  }
}
