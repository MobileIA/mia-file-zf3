var miaFile = {
    photos : {},
    count: 0,
    limit: 10
};
/**
 * Funcion que se encarga de subir los archivos
 * @param int appId
 * @param String elementId
 */
miaFile.upload = function(appId, elementId){
    // Obtener archivos seleccionados
    var files = $("#"+elementId+"_file").prop("files");
    // Verificar si se supero el maximo permitido
    if(files.length > miaFile.limit || (miaFile.photos[elementId+"Val"].length + files.length) > miaFile.limit){
        return alert("No se pueden cargar mas de 10 archivos");
    }
    // Recorremos todos los archivos seleccionados
    for(var i = 0; i < files.length; i++){
        // Obtener archivo
        var file = files[i];
        // Subimos archivo
        miaFile._uploadFile(appId, elementId, file);
   }
};
/**
 * Funcion privada que se encarga de subir individualmente cada archivo
 * @param int appId
 * @param String elementId
 * @param File file
 */
miaFile._uploadFile = function(appId, elementId, file){
    // Verificar si es una imagen
    if(file.type != "image/jpeg" && file.type != "image/png" && file.type != "image/jpg"){
        return alert("Solo se permiten imagenes.");
    }
    // Mostrar imagen que se esta cargando.
    var imageId = miaFile.count;
    // Mostramos imagen en la galeria
    miaFile.showImage(file, imageId, elementId);
    // Sumar ID de la imagen
    miaFile.count++;
    // Llamada al servidor
    $.ajax({
        url: "http://files.mobileia.com/api/upload",
        type: "POST",
        data:  miaFile._createFormData(appId, file),
        contentType: false,
        cache: false,
        processData:false,
        xhr: function(){
            // Ocultamos mensaje de cargando...
            $("#"+elementId+"_container_"+imageId+" p").hide();
            var xhr = new window.XMLHttpRequest();
            //Upload progress, request sending to server
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    percentComplete = parseInt( (evt.loaded / evt.total * 100), 10);
                    // Mostramos progressBar
                    $("#"+elementId+"_progress_"+imageId+" div").css("width", percentComplete + "%");
              }
            }, false);
            return xhr;
        },
        success: function(data){
            // Resetear input
            $("#"+elementId+"_file").val("");
            if(!data.success){
                // Mostrar mensaje de error
                return $("#"+elementId+"_container_"+imageId+" p").html("Error");
            }
            // Mostramos mensaje de que se cargo correctamente
            $("#"+elementId+"_container_"+imageId+" p").html("Cargado");
            $("#"+elementId+"_container_"+imageId+" p").show();
            // Ocultamos progressBar
            $("#"+elementId+"_progress_"+imageId+"").hide();
            // Cargamos datos en el input oculto
            miaFile.photos[elementId+"Val"].push(data.response[0]);
            $("#"+elementId).val(JSON.stringify(miaFile.photos[elementId+"Val"]));
            // Cambiamos nombre del boton
            $("#"+elementId+"_button span").html("Subir otra");
        }	        
   });
};
/**
 * Crea el FormData para la petición
 * @param int appId
 * @param File file
 * @returns FormData
 */
miaFile._createFormData = function(appId, file){
    // Crear formData
    var formData = new FormData();
    // Adjuntamos datos del archivo seleccionado
    formData.append("file[0]", file);
    // Adjuntamos AppID
    formData.append("app_id", appId);
    // Devolvemos formData
    return formData;
};
/**
 * Muestra una imagen local que se quiere subir
 * @param File file
 * @param string imageId
 * @param string elementId
 */
miaFile.showImage = function(file, imageId, elementId){
    // Crear el reader de archivos locales
    var reader = new FileReader();
    // Funcion que se ejecuta una vez obtenido el archivo
    reader.onload = function(e){
        $("#"+elementId+"_gallery").append('<div id="'+elementId+'_container_'+imageId+'" class="item-image" style="display: inline-block;"><img id="'+elementId+'_image_'+imageId+'" src="'+e.target.result+'" style="width: 100px; height: 100px; object-fit: cover;" /><p class="text-center" style="display: none;">Cargando...</p><div id="'+elementId+'_progress_'+imageId+'" class="progress progress-sm active" style="width: 100px;margin-bottom: 5px;"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div></div></div>');
    };
    // Iniciar archivo
    reader.readAsDataURL(file);
};
/**
 * Imprime las imagenes de la galeria
 * @param String elementId
 */
miaFile.printImages = function(elementId){
    // Borramos todo el contenido de esta galeria.
    $("#"+elementId+"_gallery").html("");
    // recorremos las imagenes cargadas
    for(var i = 0; i < miaFile.photos[elementId+"Val"].length; i++){
        // Obtenemos Imagen
        var image = miaFile.photos[elementId+"Val"][i];
        // Generamos HTML de la imagen
        $("#"+elementId+"_gallery").append('<div id="'+elementId+'_container_'+image.id+'" data-element="'+elementId+'" data-num="'+i+'" class="item-image" style="display: inline-block;"><img id="'+elementId+'_image_'+image.id+'" src="'+image.url+'" style="width: 100px; height: 100px; object-fit: cover;" /><p class="text-center"><a href="#" onclick="return miaFile.delete(this);">Eliminar</a></p></div>');
    }
    // Verificamos si ya hay 1 cargada para cambiar el nombre del boton.
    if(miaFile.photos[elementId+"Val"].length > 0){
        // Cambiamos nombre del boton
        $("#"+elementId+"_button span").html("Subir otra");
    }
};
/**
 * Elimina una imagen de la galeria
 * @param HTMLElement element
 * @returns Boolean
 */
miaFile.delete = function(element){
    // Obtenemos el container del elemento
    var container = $(element).parent().parent();
    // Obtenemos el ID del elemento
    var elementId = container.attr("data-element");
    // Ocultamos contenedor de la imagen a borrar
    container.hide();
    // Obtenemos el numero de imagen subido
    var num = parseInt(container.attr("data-num"));
    // Asignamos el valor para eliminar
    miaFile.photos[elementId+"Val"][num].deleted = 1;
    // Cargamos datos al input
    $("#"+elementId).val(JSON.stringify(miaFile.photos[elementId+"Val"]));
    return false;
};