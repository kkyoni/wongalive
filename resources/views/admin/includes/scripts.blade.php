 <!-- jQuery -->
 <script src="{{ asset('assets/admin/js/jquery-3.1.1.min.js') }}"></script>
<!--  <script src="{{ asset('assets/front/vendors/bower_components/jquery/dist/jquery.min.js')}}"></script> -->
 <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->


 <!-- Bootstrap Core JavaScript -->
 <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/bootstrap.js') }}"></script>
 <script src="{{ asset('assets/admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
 <script src="{{ asset('assets/admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js"></script>

 <script src="{{ asset('assets/admin/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
 <!-- Image cropper -->
 <script src="{{ asset('assets/admin/js/plugins/cropper/cropper.min.js') }}"></script>

 <!-- Date range use moment.js same as full calendar plugin -->
 <script src="{{ asset('assets/admin/js/plugins/fullcalendar/moment.min.js') }}"></script>

 <!-- Date range picker -->
 <script src="{{ asset('assets/admin/js/plugins/daterangepicker/daterangepicker.js') }}"></script>

 <script src="{{ asset('assets/admin/js/plugins/select2/select2.full.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/inspinia.js') }}"></script>
 <script src="{{ asset('assets/admin/js/plugins/pace/pace.min.js') }}"></script>
 <script src="{{ asset('assets/admin/js/plugins/iCheck/icheck.min.js') }}"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.js"></script>

 <!-- Tags Input -->
 <script src="{{ asset('assets/admin/js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

 <!-- Dual Listbox -->
 <script src="{{ asset('assets/admin/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js') }}"></script>

 <script type="text/javascript" src="http://demos.codexworld.com/multi-select-dropdown-list-with-checkbox-jquery/multiselect/jquery.multiselect.js">
 </script>
 <script>
 	$(document).ready(function(){

 		$('.tagsinput').tagsinput({
 			tagClass: 'label label-primary'
 		});

 		var $image = $(".image-crop > img")
 		$($image).cropper({
 			aspectRatio: 1.618,
 			preview: ".img-preview",
 			done: function(data) {
                    // Output the result data for cropping image.
                }
            });

 		var $inputImage = $("#inputImage");
 		if (window.FileReader) {
 			$inputImage.change(function() {
 				var fileReader = new FileReader(),
 				files = this.files,
 				file;

 				if (!files.length) {
 					return;
 				}

 				file = files[0];

 				if (/^image\/\w+$/.test(file.type)) {
 					fileReader.readAsDataURL(file);
 					fileReader.onload = function () {
 						$inputImage.val("");
 						$image.cropper("reset", true).cropper("replace", this.result);
 					};
 				} else {
 					showMessage("Please choose an image file.");
 				}
 			});
 		} else {
 			$inputImage.addClass("hide");
 		}

 		$("#download").click(function() {
 			window.open($image.cropper("getDataURL"));
 		});

 		$("#zoomIn").click(function() {
 			$image.cropper("zoom", 0.1);
 		});

 		$("#zoomOut").click(function() {
 			$image.cropper("zoom", -0.1);
 		});

 		$("#rotateLeft").click(function() {
 			$image.cropper("rotate", 45);
 		});

 		$("#rotateRight").click(function() {
 			$image.cropper("rotate", -45);
 		});

 		$("#setDrag").click(function() {
 			$image.cropper("setDragMode", "crop");
 		});

 		// var elem = document.querySelector('.js-switch');
 		// var switchery = new Switchery(elem, { color: '#1AB394' });

 		// var elem_2 = document.querySelector('.js-switch_2');
 		// var switchery_2 = new Switchery(elem_2, { color: '#ED5565' });

 		// var elem_3 = document.querySelector('.js-switch_3');
 		// var switchery_3 = new Switchery(elem_3, { color: '#1AB394' });

 		// var elem_4 = document.querySelector('.js-switch_4');
 		// var switchery_4 = new Switchery(elem_4, { color: '#f8ac59' });
 		// switchery_4.disable();

 		$('.i-checks').iCheck({
 			checkboxClass: 'icheckbox_square-green',
 			radioClass: 'iradio_square-green'
 		});

 		// $('.demo1').colorpicker();

 		// var divStyle = $('.back-change')[0].style;
 		// $('#demo_apidemo').colorpicker({
 		// 	color: divStyle.backgroundColor
 		// }).on('changeColor', function(ev) {
 		// 	divStyle.backgroundColor = ev.color.toHex();
 		// });

 		// $('.clockpicker').clockpicker();

 		$('input[name="daterange"]').daterangepicker();

 		$('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

 		$('#reportrange').daterangepicker({
 			format: 'DD/MM/YYYY',
 			startDate: moment().subtract(29, 'days'),
 			endDate: moment(),
 			minDate: '01/01/2012',
 			maxDate: '12/31/2015',
 			dateLimit: { days: 60 },
 			showDropdowns: true,
 			showWeekNumbers: true,
 			timePicker: false,
 			timePickerIncrement: 1,
 			timePicker12Hour: true,
 			ranges: {
 				'Today': [moment(), moment()],
 				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
 				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
 				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
 				'This Month': [moment().startOf('month'), moment().endOf('month')],
 				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
 			},
 			opens: 'right',
 			drops: 'down',
 			buttonClasses: ['btn', 'btn-sm'],
 			applyClass: 'btn-primary',
 			cancelClass: 'btn-default',
 			separator: ' to ',
 			locale: {
 				applyLabel: 'Submit',
 				cancelLabel: 'Cancel',
 				fromLabel: 'From',
 				toLabel: 'To',
 				customRangeLabel: 'Custom',
 				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
 				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
 				firstDay: 1
 			}
 		}, function(start, end, label) {
 			console.log(start.toISOString(), end.toISOString(), label);
 			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
 		});

 		$(".select2_demo_1").select2();
 		$(".select2_demo_2").select2();
 		$(".select2_demo_3").select2({
 			placeholder: "Select a state",
 			allowClear: true
 		});


 		// $(".touchspin1").TouchSpin({
 		// 	buttondown_class: 'btn btn-white',
 		// 	buttonup_class: 'btn btn-white'
 		// });

 		// $(".touchspin2").TouchSpin({
 		// 	min: 0,
 		// 	max: 100,
 		// 	step: 0.1,
 		// 	decimals: 2,
 		// 	boostat: 5,
 		// 	maxboostedstep: 10,
 		// 	postfix: '%',
 		// 	buttondown_class: 'btn btn-white',
 		// 	buttonup_class: 'btn btn-white'
 		// });

 		// $(".touchspin3").TouchSpin({
 		// 	verticalbuttons: true,
 		// 	buttondown_class: 'btn btn-white',
 		// 	buttonup_class: 'btn btn-white'
 		// });

 		$('.dual_select').bootstrapDualListbox({
 			selectorMinimalHeight: 160
 		});
	 // $("#ionrange_1").ionRangeSlider({
	 // 	min: 0,
	 // 	max: 5000,
	 // 	type: 'double',
	 // 	prefix: "$",
	 // 	maxPostfix: "+",
	 // 	prettify: false,
	 // 	hasGrid: true
	 // });

	 // $("#ionrange_2").ionRangeSlider({
	 // 	min: 0,
	 // 	max: 10,
	 // 	type: 'single',
	 // 	step: 0.1,
	 // 	postfix: " carats",
	 // 	prettify: false,
	 // 	hasGrid: true
	 // });

	 // $("#ionrange_3").ionRangeSlider({
	 // 	min: -50,
	 // 	max: 50,
	 // 	from: 0,
	 // 	postfix: "°",
	 // 	prettify: false,
	 // 	hasGrid: true
	 // });

	 // $("#ionrange_4").ionRangeSlider({
	 // 	values: [
	 // 	"January", "February", "March",
	 // 	"April", "May", "June",
	 // 	"July", "August", "September",
	 // 	"October", "November", "December"
	 // 	],
	 // 	type: 'single',
	 // 	hasGrid: true
	 // });

	 // $("#ionrange_5").ionRangeSlider({
	 // 	min: 10000,
	 // 	max: 100000,
	 // 	step: 100,
	 // 	postfix: " km",
	 // 	from: 55000,
	 // 	hideMinMax: true,
	 // 	hideFromTo: false
	 // });

	 // $(".dial").knob();

	 // var basic_slider = document.getElementById('basic_slider');

	 // noUiSlider.create(basic_slider, {
	 // 	start: 40,
	 // 	behaviour: 'tap',
	 // 	connect: 'upper',
	 // 	range: {
	 // 		'min':  20,
	 // 		'max':  80
	 // 	}
	 // });

	 // var range_slider = document.getElementById('range_slider');

	 // noUiSlider.create(range_slider, {
	 // 	start: [ 40, 60 ],
	 // 	behaviour: 'drag',
	 // 	connect: true,
	 // 	range: {
	 // 		'min':  20,
	 // 		'max':  80
	 // 	}
	 // });

	 // var drag_fixed = document.getElementById('drag-fixed');

	 // noUiSlider.create(drag_fixed, {
	 // 	start: [ 40, 60 ],
	 // 	behaviour: 'drag-fixed',
	 // 	connect: true,
	 // 	range: {
	 // 		'min':  20,
	 // 		'max':  80
	 // 	}
	 // });
	 // $('.chosen-select').chosen({width: "100%"});

});
</script>
{!! Notify::render() !!}
@yield('scripts')
