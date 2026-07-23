<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $sf_row['institution_name']; ?></title>
    <meta name="description" content="RFID DTR">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    
    <!-- data table -->
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
    
    <!-- Select2 CSS for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
    div.dataTables_wrapper {
        margin-bottom: 3em;
    }
    
    table.display {
      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
    
    table.display td, table.display th {
      border: 1px solid #ddd;
      padding: 8px;
    }
    
    table.display tr:nth-child(even){background-color: #f2f2f2;}
    
    table.display tr:hover {background-color: #ddd;}
    
    table.display th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #02c748;
      color: white;
    }
    
    
    table {
      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
    
    table td, table th {
      border: 1px solid #ddd;
      padding: 8px;
    }
    
    

    </style>
     
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css" />
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css" />
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="css/fontastic.css" />
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="css/grasp_mobile_progress_circle-1.0.0.min.css" />
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css" />
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet" />
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="css/custom.css" />
    
     
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/<?php echo $sf_row['logo']; ?>" />
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
 
  <style>
     
    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }
    
    /* Style the buttons inside the tab */
    .tab a {
        background-color: #f1f1f1;
        color: #02c748 !important;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 8px 10px;
        transition: 0.3s;
        font-size: 14px;
        text-decoration-line: none;
  }
        
  
    
    /* Change background color of buttons on hover */
    .tab a:hover {
        color: whitesmoke !important;
        background-color: #02c748;
    }
    
    /* Create an active/current tablink class */
    .tab a.active {
        
        background-color: #02c748;
        color: #fff !important;
        font-weight: bold;
        
    }
    
    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 6px;
        border-top: none;
    } 
    </style>
    
    
    
    <style>
    .dropbtn {
        background-color: #02c748;
        color: white;
        font-size: 16px;
        border: none;
        margin-top: 5px;
        height: 35px;
        padding: 4px 12px 4px 12px;
    }
    
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }
    
    .dropdown-content a {
        color: black;
        padding: 4px 12px 4px 12px;
        text-decoration: none;
        display: block;
    }
    
    .dropdown-content a:hover {
        background-color: #3e8e41;
        color: #fff;
        border: solid 1px #fff;
        
        }
    
    .dropdown:hover .dropdown-content {display: block;}
    
    .dropdown:hover .dropbtn { background-color: #3e8e41; border-color: #2074b1; }
    </style>
    
  </head>
  