<?php

namespace HcodeEcom\modules\product\models;

class Product{
  private $id;
  private $name;
  private $price;
  private $width;
  private $height;
  private $length;
  private $weight;
  private $url;
  private $dateRegister;
  private $photo;
  
  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getPrice()
  {
    return $this->price;
  }

  public function setPrice($price)
  {
    $this->price = $price;
  }

  public function getWidth()
  {
    return $this->width;
  }

  public function setWidth($width)
  {
    $this->width = $width;
  }

  public function getHeight()
  {
    return $this->height;
  }

  public function setHeight($height)
  {
    $this->height = $height;
  }

  public function getLength()
  {
    return $this->length;
  }

  public function setLength($length)
  {
    $this->length = $length;
  }

  public function getWeight()
  {
    return $this->weight;
  }

  public function setWeight($weight)
  {
    $this->weight = $weight;
  }

  public function getUrl()
  {
    return $this->url;
  }

  public function setUrl($url)
  {
    $this->url = $url;
  }

  public function getDateRegister()
  {
    return $this->dateRegister;
  }

  public function setDateRegister($dateRegister)
  {
    $this->dateRegister = $dateRegister;
  }

  public function setPhoto($photo)
  {
    $this->photo = $photo;
  }

  public function getPhoto()
  {
    return $this->photo;
  }

}