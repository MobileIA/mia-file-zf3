<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MIAFile\View\Helper;

/**
 * Description of FormMobileiaPhoto
 *
 * @author matiascamiletti
 */
class FormMobileiaPhoto  extends FormMobileiaFile
{
    /**
     * 
     * @param \Backend\Form\Element\MobileiaPhoto $element
     * @return string
     */
    protected function render($element)
    {
        // Creamos galeria html
        $html = '<div id="'.$element->getName().'_photo_container" class="gallery"></div>';
        // Creamos el input visible para seleccionar archivo
        $html .= '<input id="'.$element->getName().'_file" type="file" class="form-control" style="display: none;">';
        // Creamos input para almacenar los datos
        $html .= '<input id="'.$element->getName().'" name="'.$element->getName().'" type="hidden" value="'.$element->getValue().'">';
        // Creamos boton para mostrar
        $html .= '<a id="'.$element->getName().'_button" class="btn btn-app" onclick="$(\'#'.$element->getName().'_file\').click();"><i class="fa fa-upload"></i> <span>Subir archivo</span></a>';
        // Devolvemos HTML
        return $html;
    }
    
    protected function insertChangeScript($element)
    {
        $this->headScript->appendScript('mobileiaPhoto.'.$element->getName().'Val = "' .$element->getValue() . '";');
        $this->headScript->appendScript('if(mobileiaPhoto.'.$element->getName().'Val != ""){
            // Imprimir imagenes
            mobileiaPhotoPrintImage("'.$element->getName().'");
}');
        $this->headScript->appendScript('$("#'.$element->getName().'_file").change(function(){ mobileiaPhotoFunc(4, "'.$element->getName().'"); });');
    }
    
    protected function insertFunctionScript()
    {
        $this->headScript->appendScript('
var mobileiaPhoto = {};
function mobileiaPhotoFunc(appId, elementId){
    // Obtener archivo
    var file = $("#"+elementId+"_file").prop("files")[0];
    // Verificar si es una imagen
    if(file.type != "image/jpeg" && file.type != "image/png" && file.type != "image/jpg"){
        return alert("Solo se permiten imagenes.");
    }
    // Mostrar imagen que se esta cargando.
    mobileiaPhotoShowImage(file, elementId);
    // Crear formData
    var form_data = new FormData();
    // Adjuntamos datos del archivo seleccionado
    form_data.append("file[0]", file);
    // Adjuntamos AppID
    form_data.append("app_id", appId);
    // Llamada al servidor
    $.ajax({
        url: "http://files.mobileia.com/api/upload",
        type: "POST",
        data:  form_data,
        contentType: false,
        cache: false,
        processData:false,
        success: function(data){
            // Resetear input
            $("#"+elementId+"_file").val("");
            if(!data.success){
                // Mostrar mensaje de error
                return $("#"+elementId+"_container p").html("Error");
            }
            // Mostramos mensaje de que se cargo correctamente
            $("#"+elementId+"_container p").html("Cargado");
            // Cargamos datos en el input oculto
            $("#"+elementId).val("http://files.mobileia.com/" + data.response[0].path);
            // Cambiamos nombre del boton
            $("#"+elementId+"_button span").html("Cambiar");
        }	        
   });
}
function mobileiaPhotoShowImage(file, elementId){
    var reader = new FileReader();	
    reader.onload = function(e){
        $("#"+elementId+"_photo_container").html("");
        $("#"+elementId+"_photo_container").append(\'<div id="\'+elementId+\'_container" class="item-image" style="display: inline-block;"><img id="\'+elementId+\'_image" src="\'+e.target.result+\'" style="width: 100px; height: 100px; object-fit: cover;" /><p class="text-center">Cargando...</p></div>\');
    };
    reader.readAsDataURL(file);
}
function mobileiaPhotoPrintImage(elementId){
    $("#"+elementId+"_gallery").html("");
    var imageUrl = mobileiaPhoto[elementId+"Val"];
    
    if(imageUrl == ""){
        return false;
    }
    
    $("#"+elementId+"_photo_container").append(\'<div id="\'+elementId+\'_container" data-element="\'+elementId+\'" class="item-image" style="display: inline-block;"><img id="\'+elementId+\'_image" src="\'+imageUrl+\'" style="width: 100px; height: 100px; object-fit: cover;" /><p class="text-center"></p></div>\');
    $("#"+elementId+"_button span").html("Cambiar");
}');
    }
}