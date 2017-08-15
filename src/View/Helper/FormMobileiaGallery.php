<?php

namespace MIAFile\View\Helper;

/**
 * Description of FormMobileiaGallery
 *
 * @author matiascamiletti
 */
class FormMobileiaGallery extends FormMobileiaFile
{
    /**
     * 
     * @param \Backend\Form\Element\MobileiaGallery $element
     * @return string
     */
    protected function render($element)
    {
        // Creamos galeria html
        $html = '<div id="'.$element->getName().'_gallery" class="gallery"></div>';
        // Creamos el input visible para seleccionar archivo
        $html .= '<input id="'.$element->getName().'_file" type="file" class="form-control" style="display: none;" multiple>';
        // Creamos input para almacenar los datos
        $html .= '<input id="'.$element->getName().'" name="'.$element->getName().'" type="hidden" value="'.$this->escapeHtmlAttr->__invoke($element->getValue()).'">';
        // Creamos boton para mostrar
        $html .= '<a id="'.$element->getName().'_button" class="btn btn-app" onclick="$(\'#'.$element->getName().'_file\').click();"><i class="fa fa-upload"></i> <span>Subir archivo</span></a>';
        // Devolvemos HTML
        return $html;
    }
    
    protected function insertChangeScript($element)
    {
        if($element->getValue() == ''){
            $this->headScript->appendScript('miaFile.photos.'.$element->getName().'Val = [];');
        }else{
            $this->headScript->appendScript('miaFile.photos.'.$element->getName().'Val = ' .$element->getValue() . ';');
        }
        $this->headScript->appendScript('if(miaFile.photos.'.$element->getName().'Val.length > 0){miaFile.printImages("'.$element->getName().'");}');
        $this->headScript->appendScript('$("#'.$element->getName().'_file").change(function(){ miaFile.upload(4, "'.$element->getName().'"); });');
    }
    
    protected function insertFunctionScript()
    {
        // Agregar archivo JS
        $this->headScript->appendFile($this->basePath->__invoke('/mia-file-zf3/js/gallery.js'));
    }
}