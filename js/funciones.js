cargardoAjax = false;
cargando(cargardoAjax);
if (typeof $wsUrl === 'undefined') {
	$wsUrl = "";
}

// Obtener el nombre de la página actual del documento.referrer o la URL
function obtenerNombrePaginaActual() {
	if (typeof $wsUrl !== 'undefined' && $wsUrl) return $wsUrl;

	// Si no está definida, extraer de la URL actual
	var ruta = window.location.href;
	// Remover protocolo, dominio y parámetros
	ruta = ruta.split('?')[0]; // Remover query string
	var partes = ruta.split('/').filter(function(p) { return p !== '' && p !== 'http:' && p !== 'https:'; });

	// Obtener la última parte que no sea un dominio
	var pagina = '';
	for (var i = partes.length - 1; i >= 0; i--) {
		if (partes[i] && partes[i].indexOf('localhost') === -1 && partes[i].indexOf('.') === -1) {
			pagina = partes[i];
			break;
		}
		if (partes[i] && partes[i].indexOf('localhost') === -1 && i === partes.length - 1) {
			pagina = partes[i].split(':')[0]; // Remover puerto si existe
			break;
		}
	}

	pagina = pagina.replace('.php', '').replace('?', '');
	if (pagina && pagina !== '' && pagina !== 'index') {
		return pagina.charAt(0).toUpperCase() + pagina.slice(1);
	}
	return "";
}

//Function ajax que se encargar de consular el WEBSERVICES
//Esta funcion ajax es usada por todas las grillas del sistema para select, insert , delete, update
function enviarPeticiones($data, $funcionEjecutar, $url= $wsUrl , $type='json'){
	//console.log($data); return;
	if(!cargardoAjax){
		// Si $url está vacía, intentar obtenerla automáticamente
		if (!$url || $url === '') {
			$url = obtenerNombrePaginaActual();
		}

		// Validar que la URL no esté vacía
		if (!$url || $url === '') {
			alertMensaje("Error", "No se pudo determinar el servicio web a llamar");
			cargando(false);
			return;
		}

		console.log('enviarPeticiones - url: ' + $url);
		console.log('enviarPeticiones - data: ', $data);
		console.log('enviarPeticiones - callback: ' + $funcionEjecutar.name);
		$.ajax({
		dataType: $type,
		type:'POST',
		data:$data,
		url: 'ws/ws' + $url + '.php',
		beforeSend: function(){ cargando(true) ;},
		success: function(json) {
			cargando(false) ;
			//$funcionEjecutar(json);
			if($type.localeCompare('json') > -1){
				 if (typeof json.error == 'undefined'  || !json.error ) {

						$funcionEjecutar(json.resultado);

				 }else{
					alertMensaje("Problemas para traer datos ;( " ,JSON.stringify(json.resultado));
				 }
			}else{
				$funcionEjecutar(json);
			}

		},
		error: function() { alertMensaje("Api Web Service", "Error al conectar con el WS inutil") ; cargando(false) ; }
		});
	}
}

//alertMensaje("ERROR", "Error al conectar con el WS", "danger");
function descargarRegistro(json){
	  var $a = $('<a />', {
		'href': $baseUrl+'descargar.php?archivo='+json.resultados,
		'download': $baseUrl+'descargar.php?archivo='+json.resultados,
		'text': "click"
	  }).hide().appendTo("body")[0].click();
	  //window.open('ws/descargar.php?archivo='+data);
}

function descargarResultado(data) {
	 window.open('ws/descargar.php?archivo='+data);
}
function descargar($accion) {
	enviarPeticiones($('#formBuscar').serialize() + "&accion=" + $accion, descargarResultado);	
}

/*
function insertRegistro(json){
	$("#insertar").modal('hide');
	alertMensaje("Insertado", json.resultados, "info");
	$("#formularioINSERT")[0].reset() //resetear formulario al insertar
}

function actualizarRegistro(json){
	$("#actualizar").modal('hide');
	$("#formularioActualizar")[0].reset() //resetear formulario al formularioActualizar	
}

function eliminarRegistro(json){
	$("#eliminar").modal('hide');	
}
*/

function eliminarRegistro($id, $texto){
	$("#modalEliminar").html('Seguro deseas eliminar ' + $texto + ' ? ');
	$("#eliminarRegistro #id").val($id);
	modalEliminar.show();
}

//Funcion mostrar PRE LOADER (CARGANDO)
function cargando(n){
	if(n)
	{
		$("#preloader").css('top','50%');
		$("#preloader").show();
		$("button").addClass('disabled');
	}else{
		cargardoAjax = false;
		$("#preloader").hide();
		$("button").removeClass('disabled');
	}
}
	try{
		var modalMensaje = new bootstrap.Modal(document.getElementById('modal-mensaje'), {
			keyboard: false
		})

		var modalEliminar = new bootstrap.Modal(document.getElementById('modal-eliminar'), {
			keyboard: false
		})
		/*
		function modalpago(){
			modalMediopago.show();
		}
		*/
	}catch(err){
			console.log(err);
	}

	
function irUrl(url, blank=false){
	if(blank){
		window.open(url);
	}else{
		window.location.href = url;
	}
	
	
}
//Funcion mostrar mensaje alertMensaje("Nueva Cancha Insertado")
function alertMensaje(titulo, mensaje, color="danger"){
	modalMensaje.show();
	$("#modal-mensaje .modal-title").html(titulo);
	$("#modal-mensaje .modal-body").html('<div  class="alert alert-'+color+'">'+mensaje+'</div>');
}

function verDetalle($data){
	enviarPeticiones($data, resultadosDetalle);
  
}

function formRegistro($formulario, $accion, $validar, $ejecutar, $url=null) {
	 
	if($validar){
		if($url==null){
			enviarPeticiones($('#'+$formulario).serialize() + "&accion=" + $accion , $ejecutar);
		}else{
			enviarPeticiones($('#'+$formulario).serialize() + "&accion=" + $accion , $ejecutar, $url);
		}
	}
}


$datosRegistro  = table = null;
function registroInsertado(resultado){
	modalEliminar.hide();
	$datosRegistro = (typeof resultado.registro != 'undefined'  || !resultado.error ) ? resultado.registro : null;
	if(typeof resultado.titulo != 'undefined'){
		alertMensaje(resultado.titulo, resultado.mensaje, (resultado.error) ? "danger" : "success");
	} 
	if(typeof resultado.recargar != 'undefined' && resultado.recargar){
		try{
			table.destroy();
			//modalRegistroPagos.hide();
		}catch(err){
			console.log(err)
		}
		
		buscarRegistro();
		try {
		  $("#formRegisto")[0].reset()
		}
		catch (e) {
		   // sentencias para manejar cualquier excepción
		   console.log(e); // pasa el objeto de la excepción al manejador de errores
		}
		
	} 	
}


//<i class="fa fa-file-pdf-o" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-file-pdf-o" aria-label="fa fa-file-pdf-o"></i>

function paratablas(buscador=false)	{		
		$texto = (buscador) ?  '' : ['excel', 'pdf' , { extend: 'alert', text: '<i class="fa fa-plus-square"></i>' }];
		$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-danger';
		//buttons: [ 'excel', 'pdf', null  ],
		table = $('#file-datatable').DataTable({ ////['copy', 'excel', 'pdf', 'colvis', {
		destroy: true,
		buttons: $texto,
		order: [],
		language: {
				processing: "Procesando...",
				info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
				lengthMenu: "Mostrar _MENU_ ",
				zeroRecords: "No se encontraron resultados",
				emptyTable: "Ningún dato disponible en esta tabla",
				infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				search: "Buscar:",
				infoThousands: ",",
				loadingRecords: "Cargando...",
				paginate: {
					"first": "Primero",
					"last": "Último",
					"next": "Siguiente",
					"previous": "Anterior"
				},
                buttons: {
				copy: "Copiar",
				colvis: "Visibilidad",
				collection: "Colección",
				colvisRestore: "Restaurar visibilidad",
				copyKeys: "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
				copySuccess: {
					"1": "Copiada 1 fila al portapapeles",
					"_": "Copiadas %ds fila al portapapeles"
				},
				copyTitle: "Copiar al portapapeles",
				csv: "CSV",
				excel: '<i class="fa fa-file-text" data-bs-toggle="tooltip" title="Exportar a Excel" data-bs-original-title="Exportar a Excel"></i>',
				pageLength: {
					"-1": "Mostrar todas las filas",
					"_": "Mostrar %d filas"
				},
				pdf: '<i class="fa fa-file-pdf" data-bs-toggle="tooltip" title="Exportar a PDF" data-bs-original-title="Exportar a PDF""></i>',
				print: "Imprimir"
			},
			searchBuilder: {
				count: "{total}",
				countFiltered: "{shown} ({total})",
				emptyPanes: "Sin paneles de búsqueda",
				loadMessage: "Cargando paneles de búsqueda",
				title: "Filtros Activos - %d",
				showMessage: "Mostrar Todo",
				collapseMessage: "Colapsar Todo"
			},
			searchPlaceholder: 'Buscarkk...',
			scrollX: "100%",
			sSearch: '',
        },
		searching: false
		

	});
	try {
		table.buttons().container().appendTo('#file-datatable_wrapper .col-md-6:eq(0)');
		//table.reload();
		//table.ajax.reload();
	}catch(err){
		console.log(err);
	}
}
	try {		
		$('.datetime').datetimepicker({
			locale: 'es',
			format: "d/m/Y H:i"
		  });
		$.datetimepicker.setLocale('es');   
		/*
		$('.datetime').datetimepicker({
			format: "d/m/Y H:i"
		});
		*/

	}catch(err){
			console.log(err);
	}
	
function editorMayor(){
            // This sample still does not showcase all CKEditor 5 features (!)
            // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
            CKEDITOR.ClassicEditor.create(document.getElementById("editor"), {
                // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
                toolbar: {
                    items: [
                        'exportPDF','exportWord', '|',
                        'findAndReplace', 'selectAll', '|',
                        'heading', '|',
                        'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                        'bulletedList', 'numberedList', 'todoList', '|',
                        'outdent', 'indent', '|',
                        'undo', 'redo',
                        '-',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                        'alignment', '|',
                        'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                        'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                        'textPartLanguage', '|',
                        'sourceEditing'
                    ],
                    shouldNotGroupWhenFull: true
                },
                // Changing the language of the interface requires loading the language file using the <script> tag.
                // language: 'es',
                list: {
                    properties: {
                        styles: true,
                        startIndex: true,
                        reversed: true
                    }
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                    ]
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
                placeholder: 'Welcome to CKEditor 5!',
                // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
                fontFamily: {
                    options: [
                        'default',
                        'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace',
                        'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif',
                        'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif',
                        'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
                fontSize: {
                    options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                    supportAllValues: true
                },
                // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
                // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
                htmlSupport: {
                    allow: [
                        {
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }
                    ]
                },
                // Be careful with enabling previews
                // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
                htmlEmbed: {
                    showPreviews: true
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
                link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
                mention: {
                    feeds: [
                        {
                            marker: '@',
                            feed: [
                                '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                                '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                                '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                                '@sugar', '@sweet', '@topping', '@wafer'
                            ],
                            minimumCharacters: 1
                        }
                    ]
                },
                // The "super-build" contains more premium features that require additional configuration, disable them below.
                // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
                removePlugins: [
                    // These two are commercial, but you can try them out without registering to a trial.
                    // 'ExportPdf',
                    // 'ExportWord',
                    'CKBox',
                    'CKFinder',
                    'EasyImage',
                    // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                    // Storing images as Base64 is usually a very bad idea.
                    // Replace it on production website with other solutions:
                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                    // 'Base64UploadAdapter',
                    'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory',
                    'PresenceList',
                    'Comments',
                    'TrackChanges',
                    'TrackChangesData',
                    'RevisionHistory',
                    'Pagination',
                    'WProofreader',
                    // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                    // from a local file system (file://) - load this site via HTTP server if you enable MathType
                    'MathType'
                ],
				language: 'es'
            });
}