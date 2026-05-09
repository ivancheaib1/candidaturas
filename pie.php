</main>
                <footer class="py-4 bg-light mt-auto" >
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; <?php echo $empresa["nombre"].' ('.date('Y').')' ?></div>
                            <div>
                               
                                &middot;
                                <a href="#">Soporte</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
		
	<div id="preloader">
		<div id="preloader-inner"></div>
	</div>

	
	
	
	      <div class="modal fade" id="modal-mensaje" tabindex="-1" style="z-index: 9999 !important">
          <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Default Modal</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>One fine body&hellip;</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary  pull-left" data-bs-dismiss="modal">Cerrar</button>
              <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
			  
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

	  
	 <div class="modal fade" id="modal-eliminar" tabindex="-1">
          <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Eliminar Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
				<div class="alert alert-danger" id="modalEliminar" role="alert"></div>
                <form  class="row" role="form" autocomplete="off" id="eliminarRegistro">
						  <input type="hidden" id="id" name="id" value="" >
				 </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary  pull-left" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="formRegistro('eliminarRegistro', 'eliminarRegistro', true, registroInsertado)">Si, eliminar</button>  
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
 
 
	  
	  
        <!--  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>-->
		<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="assets/bootstrap/js/popper.min.js"></script>
        <script src="js/scripts.js"></script>

		<!-- DATA TABLE JS-->
		<script src="assets/datatable/js/jquery.dataTables.min.js"></script>
		<script src="assets/datatable/js/dataTables.bootstrap5.js"></script>
		<script src="assets/datatable/js/dataTables.buttons.min.js"></script>
		<script src="assets/datatable/js/buttons.bootstrap5.min.js"></script>
		<script src="assets/datatable/js/jszip.min.js"></script>
		<script src="assets/datatable/pdfmake/pdfmake.min.js"></script>
		<script src="assets/datatable/pdfmake/vfs_fonts.js"></script>
		<script src="assets/datatable/js/buttons.html5.min.js"></script>
		<script src="assets/datatable/js/buttons.print.min.js"></script>
		<script src="assets/datatable/js/buttons.colVis.min.js"></script>
		<script src="assets/datatable/dataTables.responsive.min.js"></script>
		<script src="assets/datatable/responsive.bootstrap5.min.js"></script>

		<script src="assets/bootstrap-multiselect/js/BsMultiSelect.min.js"></script>
		<script src="assets/bootstrap-datepicker/jquery.datetimepicker.full.min.js"></script>
		<!--
		<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/super-build/ckeditor.js"></script>
		<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/super-build/translations/es.js"></script>
		-->
		<script src="assets/bootstrap-select/bootstrap-select.min.js?v2"></script>
		<script src="js/funciones.js?v02"></script>	
 
 	
    </body>
</html>
