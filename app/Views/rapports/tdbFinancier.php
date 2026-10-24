<?php

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Tableau de bord financier</title>
  <link rel="icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
  <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/Ionicons/css/ionicons.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="/assets/css/AdminLTE.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" />
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/bower_components/font-awesome/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/assets/bower_components/Ionicons/css/ionicons.min.css" />
  <!-- Date Picker -->
  <link rel="stylesheet" href="/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
  <link rel="stylesheet" href="/assets/plugins/iCheck/all.css" />
  <link href="../../assets/styles.css" rel="stylesheet" />

  <!-- ApexCharts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

  <style>
    .chart-box {
      background: #fff;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,.12);
      margin-bottom: 20px;
      padding: 15px;
    }
    .chart-box h4 {
      margin-top: 0;
      color: #333;
      border-bottom: 2px solid #3c8dbc;
      padding-bottom: 8px;
    }
    .kpi-card {
      background: #fff;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,.12);
      padding: 20px;
      text-align: center;
      margin-bottom: 20px;
    }
    .kpi-card .kpi-value {
      font-size: 28px;
      font-weight: 700;
      color: #3c8dbc;
    }
    .kpi-card .kpi-label {
      font-size: 13px;
      color: #777;
      margin-top: 4px;
    }
    .kpi-card.green .kpi-value { color: #00a65a; }
    .kpi-card.red .kpi-value { color: #dd4b39; }
    .kpi-card.blue .kpi-value { color: #3c8dbc; }
    .kpi-card.yellow .kpi-value { color: #f39c12; }
    .popover-title {
      background: darkblue;
      color: white;
      font-weight: bold;
      min-width: 200px;
    }
    .popover-content {
      background: lightgray;
      color: darkblue;
      min-width: 200px;
    }
    .date-filter-bar {
      background: #f4f4f4;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 15px;
      margin-bottom: 20px;
    }
  </style>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <script>
    var base_url = "<?php echo ci3_url(); ?>";
    $(document).ready(function () {
      $('[data-toggle="popover"]').popover();
    });
  </script>
</head>
<body class="hold-transition skin-blue">

  <!-- Header Navbar -->
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <?php echo view('sections/menu'); ?>
  </nav>

  <div class="wrapper" style="margin-left:20px; margin-top:70px; width:98%;">

    <header class="page-header">
      <h2><i class="fa fa-line-chart"></i> Tableau de bord financier</h2>
    </header>

    <!-- ========== Filtre par dates ========== -->
    <div class="date-filter-bar">
      <form method="get" action="<?php echo ci3_url('rapport/financier'); ?>" class="form-inline">
        <div class="form-group">
          <label for="datedebut">Du&nbsp;</label>
          <input type="date" class="form-control" id="datedebut" name="datedebut" value="<?php echo ($datedebut ?? ''); ?>" />
        </div>
        <div class="form-group" style="margin-left:10px;">
          <label for="datefin">Au&nbsp;</label>
          <input type="date" class="form-control" id="datefin" name="datefin" value="<?php echo ($datefin ?? ''); ?>" />
        </div>
        <button type="submit" class="btn btn-primary" style="margin-left:10px;">
          <i class="fa fa-refresh"></i> Actualiser
        </button>
      </form>
    </div>

    <!-- ========== KPI Cards (Graphique 6) ========== -->
    <div class="row">
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="kpi-card blue">
          <div class="kpi-value"><?php echo number_format($kpiRevenus, 2, ',', ' '); ?> $</div>
          <div class="kpi-label">Revenus totaux</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="kpi-card red">
          <div class="kpi-value"><?php echo number_format($kpiDepenses, 2, ',', ' '); ?> $</div>
          <div class="kpi-label">Dépenses totales</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="kpi-card <?php echo ($kpiProfitNet >= 0) ? 'green' : 'red'; ?>">
          <div class="kpi-value"><?php echo number_format($kpiProfitNet, 2, ',', ' '); ?> $</div>
          <div class="kpi-label">Profit net</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="kpi-card yellow">
          <div class="kpi-value"><?php echo number_format($kpiTonnage, 2, ',', ' '); ?></div>
          <div class="kpi-label">Tonnage total</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="kpi-card red">
          <div class="kpi-value"><?php echo number_format($kpiCoutParTonne, 2, ',', ' '); ?> $</div>
          <div class="kpi-label">Coût / tonne</div>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="kpi-card blue">
          <div class="kpi-value"><?php echo number_format($kpiRevenuParTonne, 2, ',', ' '); ?> $</div>
          <div class="kpi-label">Revenu / tonne</div>
        </div>
      </div>
    </div>

    <!-- ========== Graphique 1 : Revenus vs Dépenses par mois ========== -->
    <div class="row">
      <div class="col-md-8">
        <div class="chart-box">
          <h4><i class="fa fa-bar-chart"></i> Revenus vs Dépenses par mois</h4>
          <div id="chartRevDep"></div>
        </div>
      </div>

      <!-- ========== Graphique 2 : Répartition des dépenses (donut) ========== -->
      <div class="col-md-4">
        <div class="chart-box">
          <h4><i class="fa fa-pie-chart"></i> Répartition des dépenses</h4>
          <div id="chartDonut"></div>
        </div>
      </div>
    </div>

    <!-- ========== Graphique 3 : Revenus par type de collecte ========== -->
    <div class="row">
      <div class="col-md-12">
        <div class="chart-box">
          <h4><i class="fa fa-tasks"></i> Revenus par type de collecte</h4>
          <div id="chartStacked"></div>
        </div>
      </div>
    </div>

    <!-- ========== Graphique 4 : Rentabilité par camion ========== -->
    <div class="row">
      <div class="col-md-6">
        <div class="chart-box">
          <h4><i class="fa fa-truck"></i> Rentabilité par camion</h4>
          <div id="chartCamion"></div>
        </div>
      </div>

      <!-- ========== Graphique 5 : Évolution du profit net ========== -->
      <div class="col-md-6">
        <div class="chart-box">
          <h4><i class="fa fa-area-chart"></i> Évolution du profit net</h4>
          <div id="chartProfit"></div>
        </div>
      </div>
    </div>

    <!-- ========== Graphique 6 : Coût par tonne (radialBar) ========== -->
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <div class="chart-box">
          <h4><i class="fa fa-dashboard"></i> Performance globale</h4>
          <div id="chartRadial"></div>
        </div>
      </div>
    </div>

  </div>

  <?php echo view('sections/inc_pop_up_gen'); ?>

  <script src="/assets/jquery-ui/jquery-ui.min.js"></script>
  <script src="/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <script src="/assets/fastclick/lib/fastclick.js"></script>
  <script src="/assets/js/adminlte.min.js"></script>
  <script src="/assets/moment/moment.js"></script>
  <script src="/assets/plugins/iCheck/icheck.min.js"></script>
  <script src="/assets/js/nika.js"></script>

  <script>
  // ==============================================================
  //  Graphique 1 — Revenus vs Dépenses par mois (grouped bar)
  // ==============================================================
  var optRevDep = {
    chart: { type: 'bar', height: 380, toolbar: { show: true } },
    series: [
      { name: 'Revenus',  data: [<?php echo ($chartRevenus ?? ''); ?>] },
      { name: 'Dépenses', data: [<?php echo ($chartDepenses ?? ''); ?>] }
    ],
    xaxis: { categories: [<?php echo ($chartMoisLabels ?? ''); ?>], title: { text: 'Mois' } },
    yaxis: { title: { text: 'Montant ($)' },
             labels: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    colors: ['#00a65a','#dd4b39'],
    plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    legend: { position: 'top' },
    grid: { borderColor: '#e7e7e7' }
  };
  new ApexCharts(document.querySelector("#chartRevDep"), optRevDep).render();

  // ==============================================================
  //  Graphique 2 — Répartition des dépenses (donut)
  // ==============================================================
  var optDonut = {
    chart: { type: 'donut', height: 380 },
    series: [<?php echo ($donutValues ?? ''); ?>],
    labels: [<?php echo ($donutLabels ?? ''); ?>],
    colors: ['#f39c12','#3c8dbc','#00a65a','#605ca8'],
    legend: { position: 'bottom' },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    responsive: [{ breakpoint: 480, options: { chart: { width: 300 }, legend: { position: 'bottom' } } }]
  };
  new ApexCharts(document.querySelector("#chartDonut"), optDonut).render();

  // ==============================================================
  //  Graphique 3 — Revenus par type de collecte (stacked bar)
  // ==============================================================
  var optStacked = {
    chart: { type: 'bar', height: 380, stacked: true, toolbar: { show: true } },
    series: [<?php echo ($stackedSeries ?? ''); ?>],
    xaxis: { categories: [<?php echo ($stackedMoisLabels ?? ''); ?>], title: { text: 'Mois' } },
    yaxis: { title: { text: 'Montant ($)' },
             labels: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    colors: [<?php echo ($stackedCouleurs ?? ''); ?>],
    plotOptions: { bar: { columnWidth: '60%', borderRadius: 2 } },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    legend: { position: 'top' },
    grid: { borderColor: '#e7e7e7' }
  };
  new ApexCharts(document.querySelector("#chartStacked"), optStacked).render();

  // ==============================================================
  //  Graphique 4 — Rentabilité par camion (horizontal bar)
  // ==============================================================
  var optCamion = {
    chart: { type: 'bar', height: Math.max(300, [<?php echo ($camRevenus ?? ''); ?>].length * 40), toolbar: { show: true } },
    series: [
      { name: 'Revenus',  data: [<?php echo ($camRevenus ?? ''); ?>] },
      { name: 'Dépenses', data: [<?php echo ($camDepenses ?? ''); ?>] }
    ],
    xaxis: { categories: [<?php echo ($camLabels ?? ''); ?>] },
    yaxis: { title: { text: 'Montant ($)' },
             labels: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    colors: ['#00a65a','#dd4b39'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 3 } },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    legend: { position: 'top' },
    grid: { borderColor: '#e7e7e7' }
  };
  new ApexCharts(document.querySelector("#chartCamion"), optCamion).render();

  // ==============================================================
  //  Graphique 5 — Évolution du profit net (area)
  // ==============================================================
  var profitData = [<?php echo ($areaProfit ?? ''); ?>];
  var profitColors = profitData.map(function(v){ return v >= 0 ? '#00a65a' : '#dd4b39'; });
  var optProfit = {
    chart: { type: 'area', height: 350, toolbar: { show: true },
             dropShadow: { enabled: true, color: '#000', top: 10, left: 3, blur: 8, opacity: 0.15 } },
    series: [{ name: 'Profit net', data: profitData }],
    xaxis: { categories: [<?php echo ($areaLabels ?? ''); ?>], title: { text: 'Mois' } },
    yaxis: { title: { text: 'Profit ($)' },
             labels: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    colors: ['#3c8dbc'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1, stops: [0, 90, 100] } },
    stroke: { curve: 'smooth', width: 3 },
    dataLabels: { enabled: true, formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:0}) + ' $'; },
                  style: { fontSize: '11px' } },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString('fr-CA',{minimumFractionDigits:2}) + ' $'; } } },
    markers: { size: 4 },
    grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } }
  };
  new ApexCharts(document.querySelector("#chartProfit"), optProfit).render();

  // ==============================================================
  //  Graphique 6 — Performance globale (radialBar)
  // ==============================================================
  var totalRev = <?php echo ($kpiRevenus ?? '') > 0 ? $kpiRevenus : 1; ?>;
  var totalDep = <?php echo ($kpiDepenses ?? ''); ?>;
  var pctMarge = Math.min(100, Math.max(0, Math.round(((totalRev - totalDep) / totalRev) * 100)));
  var optRadial = {
    chart: { type: 'radialBar', height: 320 },
    series: [pctMarge],
    labels: ['Marge nette'],
    colors: [pctMarge >= 50 ? '#00a65a' : (pctMarge >= 20 ? '#f39c12' : '#dd4b39')],
    plotOptions: {
      radialBar: {
        hollow: { size: '65%' },
        dataLabels: {
          name: { fontSize: '16px', color: '#333', offsetY: -10 },
          value: { fontSize: '30px', fontWeight: 700, color: '#333',
                   formatter: function(v){ return v + '%'; } }
        }
      }
    }
  };
  new ApexCharts(document.querySelector("#chartRadial"), optRadial).render();
  </script>

</body>
</html>
