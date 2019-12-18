<?php

namespace App\Forms;

class ButtonType extends AbstractType
{
    public function __toString(){
        return \App\Twig::i()->render('form/form_button.twig',[
            'name' => $this->name,
            'value' => $this->value,
            'classes' => implode(' ',$this->classes),
            'attributes' => implode(' ',$this->attributes),
            'options' => $this->options
        ]);
    }
}