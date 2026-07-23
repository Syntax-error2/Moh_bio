
<head>
<title><?php echo $sf_row['institution_name']; ?></title>
<meta name="description" content="RFID Attendance Monitoring with SMS">
<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
<link rel="shortcut icon" href="img/<?php echo $sf_row['logo']; ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- data table -->
     <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
     <link rel="stylesheet" type="text/css" href="DataTables/Buttons-1.6.1/css/buttons.dataTables.min.css"/>
     
    <style>
    div.dataTables_wrapper {
        margin-bottom: 3em;
    }
    
    table {
      font-family: Verdana, sans-serif;
      border-collapse: collapse;
      width: 100%;
      page-break-inside:auto;
    }
    
    table td, table th {
      border: 1px solid #ddd;
      padding: 4px;
    }
    
    table tr { page-break-inside:avoid; page-break-after:auto; }
    
    table tr:nth-child(even){background-color: #f2f2f2;}
    
    table tr:hover {background-color: #fff;} 
 
    thead { display:table-header-group; }
    tfoot { display:table-footer-group; }
    
    table th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #fff;
      color: black;
    }
    
    body{
        font-family: Verdana, sans-serif;
        font-size: 12px;
    }
    
    .pb{
       page-break-after: always; 
    }
    
    </style>
    
    <style>
    /* Flexible page size support for various paper formats */
    @media print {
        body {
            max-width: 7.5in;
            margin: 0 auto;
            padding: 0.5in;
        }
        
        @page {
            size: auto;
            margin: 0.5in;
        }
    }
    </style>

    <script src="vendor/jquery/jquery.min.js"></script>
    
</head>