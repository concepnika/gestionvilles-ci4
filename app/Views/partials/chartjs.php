<?php
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>
		<?php echo ci3lang('home_top');?>
	</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
	<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" />
	  <style>
    #chart {
            width: 70%;
            margin: 35px auto;
        }
    #chart1 {
      width: 300px;
      margin: 0 auto;
    }
    </style>
	<?php echo view('sections/heads');?>
  <?php echo view('sections/footers');?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">

		<header id="navMenuTop" class="main-header">
			<?php echo ($navtop ?? '');?>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">

				<!-- sidebar menu: : style can be found in sidebar.less -->
				<ul class="sidebar-menu" data-widget="tree" id="accordionSidebar">
					<?php echo ($navleft ?? '');?>
				</ul>
			</section>
			<!-- /.sidebar -->
		</aside>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<!--<section class="content-header">
      <h1>
        Tableau de bord
        
      </h1><small></small>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>-->

			<!-- Main content -->
			<section id="secContent" class="content">
				<?php echo ($chartsclient ?? '');?>
        
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> 1.0.00
			</div>
			<strong>
				Copyright &copy; 2019
				<a href="https://www.conceptnika.com">Concept nika </a>.
			</strong>
		</footer>


		<!-- /.control-sidebar -->
		<!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>
	</div>

	
	<!-- /.row -->
	

	<script type="text/javascript">
    
    $('#dtedeb').val ('<?php echo ($dtestart ?? ''); ?>');
    $('#dtefin').val('<?php echo ($dteend ?? ''); ?>');
    function CalculMetriques() {
      document.location = "/admin/tableau_de_bord/commandes/" + $('#dtedeb').val() + "_" + $('#dtefin').val();
    }
    function AfficheData()
    {
      document.location = "/admin/tableau_de_bord/commandes/" + $('#dtedeb').val() + "_" + $('#dtefin').val();
    }
	</script>
	<script>
		$(function () {
			
			//-------------
			//- PIE CHART -
			//-------------
			// Get context with jQuery - using jQuery's .get() method.
      var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
			var pieChart = new Chart(pieChartCanvas)
			var PieData = [ <?php echo ($pie_data ?? '');?>]
			var pieOptions = {
				//Boolean - Whether we should show a stroke on each segment
				segmentShowStroke: true,
				//String - The colour of each segment stroke
				segmentStrokeColor: '#fff',
				//Number - The width of each segment stroke
				segmentStrokeWidth: 2,
				//Number - The percentage of the chart that we cut out of the middle
				percentageInnerCutout: 30, // This is 0 for Pie charts
				//Number - Amount of animation steps
				animationSteps: 100,
				//String - Animation easing effect
				animationEasing: 'easeOutBounce',
				//Boolean - Whether we animate the rotation of the Doughnut
				animateRotate: true,
				//Boolean - Whether we animate scaling the Doughnut from the centre
				animateScale: false,
				//Boolean - whether to make the chart responsive to window resizing
				responsive: true,
				// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio: true,
				//String - A legend template
				legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
			}
			//Create pie or douhnut chart
			// You can switch between pie and douhnut using the method below.
      pieChart.Doughnut(PieData, pieOptions)
			//--------------------------------------------
  
      /*
* DONUT CHART
* -----------
*/

      var donutData = [
        { label: 'Ventes', data: 60, color: 'fuchsia' },
        { label: 'Carnets', data: 40, color: '#0073b7' }
      ]
      $.plot('#donut-chart', donutData, {
        series: {
          pie: {
            show: true,
            radius: 1,
            innerRadius: 0.5,
            label: {
              show: true,
              radius: 2 / 3,
              formatter: labelFormatter,
              threshold: 0.1
            }

          }
        },
        legend: {
          show: false
        }
      })
    /*
     * END DONUT CHART
     */
  
			//-------------------------------------------
     
    })
    
    new Vue({
      el: '#chart',
      components: {
        apexchart: VueApexCharts,
      },
      chart: {
        defaultLocale: 'fr',
        locales: [{
          "name": "fr",
          "options": {
            "months": ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
            "shortMonths": ["janv.", "févr.", "mars", "avr.", "mai", "juin", "juill.", "août", "sept.", "oct.", "nov.", "déc."],
            "days": ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
            "shortDays": ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
            "toolbar": {
              "exportToSVG": "Télécharger au format SVG",
              "exportToPNG": "Télécharger au format PNG",
              "menu": "Menu",
              "selection": "Sélection",
              "selectionZoom": "Sélection et zoom",
              "zoomIn": "Zoomer",
              "zoomOut": "Dézoomer",
              "pan": "Navigation",
              "reset": "Réinitialiser le zoom"
            }
          }
        }]
      },
      data: {
        series: [{
          data: [ <?php echo ($bar_data ?? '');?>]
        }],
        chartOptions: {
          plotOptions: {
            bar: {
              barHeight: '100%',
              distributed: true,
              horizontal: true,
              dataLabels: {
                position: 'bottom'
              },
            }
          },
          colors: [<?php echo ($color ?? '');?>
          ],
          dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
              colors: ['#fff']
            },
            formatter: function (val, opt) {
              return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
            },
            offsetX: 0,
            dropShadow: {
              enabled: true
            }
          },

          stroke: {
            width: 1,
            colors: ['#fff']
          },
          xaxis: {
            categories: [<?php echo ($name_data ?? '');?>
            ],
          },
          yaxis: {
            labels: {
              show: false
            }
          },
          title: {
            text: 'Les plus gros clients',
            align: 'center',
            floating: true
          },
          subtitle: {
            text: '',
            align: 'center',
          },
          legend: {
            position: 'bottom'
            },
          tooltip: {
            theme: 'dark',
            x: {
              show: false
            },
            y: {
              title: {
                formatter: function () {
                  return ''
                }
              }
            }
          }
        }
      }
    })

   
    
		


    /*
* Custom Label formatter
* ----------------------
*/
    function labelFormatter(label, series) {
      return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + '<br>'
        + Math.round(series.percent) + '%</div>'
    }
	</script>
</body>
</html>
