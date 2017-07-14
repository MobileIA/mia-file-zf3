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
        $html .= '<input id="'.$element->getName().'_file" type="file" class="form-control">';
        // Creamos input para almacenar los datos
        $html .= '<input id="'.$element->getName().'" name="'.$element->getName().'" type="hidden" value="'.$this->escapeHtmlAttr->__invoke($element->getValue()).'">';
        // Devolvemos HTML
        return $html;
    }
    
    protected function insertChangeScript($element)
    {
        $this->headScript->appendScript('mobileiaGalleryPhotos.'.$element->getName().'Val = ' .$element->getValue());
        $this->headScript->appendScript('if(mobileiaGalleryPhotos.'.$element->getName().'Val.length > 0){
            // Imprimir imagenes
            mobileiaGalleryPrintImages("'.$element->getName().'");
}');
        $this->headScript->appendScript('$("#'.$element->getName().'_file").change(function(){ mobileiaGalleryFunc(4, "'.$element->getName().'"); });');
    }
    
    protected function insertFunctionScript()
    {
        $this->headScript->appendScript('
var mobileiaGalleryCount = 0;
var mobileiaGalleryPhotos = {};
function mobileiaGalleryFunc(appId, elementId){
    // Obtener archivo
    var file = $("#"+elementId+"_file").prop("files")[0];
    // Verificar si es una imagen
    if(file.type != "image/jpeg" && file.type != "image/png" && file.type != "image/jpg"){
        return alert("Solo se permiten imagenes.");
    }
    // Mostrar imagen que se esta cargando.
    var imageId = mobileiaGalleryCount;
    mobileiaGalleryShowImage(file, imageId, elementId);
    // Sumar ID de la imagen
    mobileiaGalleryCount++;
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
                return $("#"+elementId+"_container_"+imageId+" p").html("Error");
            }
            // Mostramos mensaje de que se cargo correctamente
            $("#"+elementId+"_container_"+imageId+" p").html("Cargado");
            // Cargamos datos en el input oculto
            mobileiaGalleryPhotos[elementId+"Val"].push(data.response[0]);
            $("#"+elementId).val(JSON.stringify(mobileiaGalleryPhotos[elementId+"Val"]));
        }	        
   });
}
function mobileiaGalleryShowImage(file, imageId, elementId){
    var reader = new FileReader();	
    reader.onload = function(e){
        $("#"+elementId+"_gallery").append(\'<div id="\'+elementId+\'_container_\'+imageId+\'" class="item-image" style="display: inline-block;"><img id="\'+elementId+\'_image_\'+imageId+\'" src="\'+e.target.result+\'" style="width: 100px; height: 100px; object-fit: cover;" /><p class="text-center">Cargando...</p></div>\');
    };
    reader.readAsDataURL(file);
}
function mobileiaGalleryPrintImages(elementId){
    $("#"+elementId+"_gallery").html("");
    for(var i = 0; i < mobileiaGalleryPhotos[elementId+"Val"].length; i++){
        var image = mobileiaGalleryPhotos[elementId+"Val"][i];
        $("#"+elementId+"_gallery").append(\'<div id="\'+elementId+\'_container_\'+image.id+\'" data-element="\'+elementId+\'" data-num="\'+i+\'" class="item-image" style="display: inline-block;"><img id="\'+elementId+\'_image_\'+image.id+\'" src="\'+image.url+\'" style="width: 100px; height: 100px; object-fit: cover;" /><p class="text-center"><a href="#" onclick="return mobileiaGalleryDelete(this);">Eliminar</a></p></div>\');
    }
}
function mobileiaGalleryDelete(element){
    var container = $(element).parent().parent();
    var elementId = container.attr("data-element");
    container.hide();
    var num = parseInt(container.attr("data-num"));
    mobileiaGalleryPhotos[elementId+"Val"][num].deleted = 1;
    // Cargamos datos al input
    $("#"+elementId).val(JSON.stringify(mobileiaGalleryPhotos[elementId+"Val"]));
    return false;
}');
    }
}