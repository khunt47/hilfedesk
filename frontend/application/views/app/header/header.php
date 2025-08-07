<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <base href="<?php echo base_url();?>">
  <title>
  Hilfedesk - Helpdesk Application
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">

  <script src="/static/assets/js/lib/jquery-3.4.1.min.js"></script>
  <script src="/static/assets/js/lib/jquery.validate.js"></script>
  <script src="/static/assets/js/core/bootstrap.min.js"></script>
  <script src="/static/assets/js/lib/bootbox.js"></script>

  <!-- CSS Files -->
  <link href="/static/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/static/assets/css/paper-dashboard.css?v=2.0.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="/static/assets/demo/demo.css" rel="stylesheet" />
  <link rel="stylesheet" href="/static/assets/css/vue-select.min.css">
  <script src="/static/assets/js/lib/vue.min.js"></script>
  <script src="/static/assets/js/lib/axios.min.js"></script>
  <script src="/static/assets/js/lib/lodash.min.js"></script>
  <script src="/static/assets/js/lib/sweetalert2.all.min.js"></script>
  <script src="/static/assets/js/lib/date.min.js"></script>
  <script src="/static/assets/js/lib/vue-select.min.js"></script>
  <script src="/static/assets/js/plugins/chartjs.min.js"></script>
  <!-- <script src="/static/assets/js/lib/notify.min.js"></script> -->
  <!-- Trix editor -->
  <link rel="stylesheet" href="/static/assets/css/lib/trix.min.css" crossorigin="anonymous">
  <script src="/static/assets/js/lib/trix.umd.min.js" crossorigin="anonymous"></script>
  <link rel="icon" type="image/png" sizes="32x32" href="/static/assets/images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/static/assets/images/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/static/assets/images/favicon/favicon-16x16.png">
</head>
<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="success">
      <div class="logo">
        <a href="home" class="simple-text logo-normal">
          <p><img id="default_logo" class="img-responsive"
            src="/static/assets/images/logo/hilfedesk-logo-webapp.png" alt="Hilfedesk logo"></p><br><br>
          </a>
        </div>
        <div class="sidebar-wrapper" id="headerapp">
          <ul class="nav">
            <li>
              <a href="tickets/all">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Tickets</b></p>
              </a>
            </li>
            <li>
              <a href="/calls">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Calls</b></p>
              </a>
            </li>
            <!-- <li>
              <a href="tickets/projects">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Tickets</b></p>
              </a>
            </li> -->
            <li>
              <a href="customers">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Customers</b></p>
              </a>
            </li>
            <li>
              <a href="contacts">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Contacts</b></p>
              </a>
            </li>
            <li v-if="isAdmin === 'yes'">
              <a href="/admin">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Admin</b></p>
              </a>
            </li>
            <li>
              <a href="reports">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Reports</b></p>
              </a>
          </li>
          <li>
              <a href="/suggestions">
                <i class="nc-icon nc-minimal-right"></i>
                <p><b>Suggestions</b></p>
              </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand"><b>Welcome</b></a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              <li class="nav-item btn-rotate dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="nc-icon nc-single-02"></i>
                  <p><b>Profile</b></p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="my-profile">Profile</a>
                  <a class="dropdown-item" href="logout">Logout</a>
                </div>

              </li>
            </ul>
          </div>
        </div>
      </nav>
<script src="static/assets/js/app/isadmin.js"></script>