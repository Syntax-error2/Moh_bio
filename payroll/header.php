<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>HRMS - Payroll System</title>
    <meta name="description" content="MOH HRMS Payroll Management System" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="all,follow" />
    
    <!-- Favicon to prevent 404 errors -->
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAA7AAAAOwBeShxvQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAKkSURBVFiFzZdNaBNBGIafmWw2u0mTNrFpbYwKihcPHjx4EfHgxYMHQRBE8OJBEAQRvHjw4EEQxYMHQRAEQRBBEEEQQRAEEQRBBEEQQRBBEEXwZ2vTpknTZjeb/TkYk5jdTdLY0PjCwuzMfN/zzjvfzHwjqKoqR6mrqysAXABOAy1ADagCH4H3wCugpKpqLVGhSALAMeAe0AEchFYGngMPgZ9xSuIC7gF3gJYkVf8Bz4C7QDkuwA6wQvKXA7gOrAKORImEBVwBriYrGIUrcBmwRwmEBVwEziav54dzwCW0w2giLOBcknpxOAsUwgJOJ6kVlyKQDws4kZROAhwPC2hPUicB7UAbgDMs4GiSOnFRVdWnqmo5u93eDrSGBRxJUqspDgO2qIDWlDQawm63twE0hwUcSkmjIVpbW1sBbFEBzcWN4HA4AFxRAbakdBrA6XQCWKMCDqWkEwrDMCzAfn5+XgU8UQH7UtIJxWAwAFABaowVFYBqVICWkk4oDMNwAs6ogEpKOqGoVqsOAFdUQDklnVBUKhUn4I4KKKakkxilUskN2KMCfqWkkxi/f/92A/aogB8p6STG9+/fPWhbqS8q4FtKOolRLBa9aN1wMSrgU0o6iVEsFn3AXlTA+5R0EuPt27c+YC8qYDUlncRYXV31AzVgNypgOSWdxFheXg4ANeB7VMCblHQS482bNwFgB1iKCnidkk5iLC4uBoEd4FVUQF5VVW+DdeLicrlcAWAHeB4V8AJ4maBiXLwEXgN1YCku4DnwIwHFuPgBPEHrftGJ0+0sAA+ARgQb4QEwA1SiAkASEeeBKbSeaJRWlmZVVfVG3USdSl3AJNqB4wCagRqwBXwB5oGXwF/gQOAviTmyDhjuYqQAAAAASUVORK5CYII=">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAA7AAAAOwBeShxvQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAKkSURBVFiFzZdNaBNBGIafmWw2u0mTNrFpbYwKihcPHjx4EfHgxYMHQRBE8OJBEAQRvHjw4EEQxYMHQRAEQRBBEEEQQRAEEQRBBEEQQRBBEEXwZ2vTpknTZjeb/TkYk5jdTdLY0PjCwuzMfN/zzjvfzHwjqKoqR6mrqysAXABOAy1ADagCH4H3wCugpKpqLVGhSALAMeAe0AEchFYGngMPgZ9xSuIC7gF3gJYkVf8Bz4C7QDkuwA6wQvKXA7gOrAKORImEBVwBriYrGIUrcBmwRwmEBVwEziav54dzwCW0w2giLOBcknpxOAsUwgJOJ6kVlyKQDws4kZROAhwPC2hPUicB7UAbgDMs4GiSOnFRVdWnqmo5u93eDrSGBRxJUqspDgO2qIDWlDQawm63twE0hwUcSkmjIVpbW1sBbFEBzcWN4HA4AFxRAbakdBrA6XQCWKMCDqWkEwrDMCzAfn5+XgU8UQH7UtIJxWAwAFABaowVFYBqVICWkk4oDMNwAs6ogEpKOqGoVqsOAFdUQDklnVBUKhUn4I4KKKakkxilUskN2KMCfqWkkxi/f/92A/aogB8p6STG9+/fPWhbqS8q4FtKOolRLBa9aN1wMSrgU0o6iVEsFn3AXlTA+5R0EuPt27c+YC8qYDUlncRYXV31AzVgNypgOSWdxFheXg4ANeB7VMCblHQS482bNwFgB1iKCnidkk5iLC4uBoEd4FVUQF5VVW+DdeLicrlcAWAHeB4V8AJ4maBiXLwEXgN1YCku4DnwIwHFuPgBPEHrftGJ0+0sAA+ARgQb4QEwA1SiAkASEeeBKbSeaJRWlmZVVfVG3USdSl3AJNqB4wCagRqwBXwB5oGXwF/gQOAviTmyDhjuYqQAAAAASUVORK5CYII="
    
    <!-- data table -->
    <link rel="stylesheet" type="text/css" href="../DataTables/datatables.min.css"/>
    
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
      background-color: #2b90d9;
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
    <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="../vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="../css/fontastic.css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="../css/grasp_mobile_progress_circle-1.0.0.min.css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="../vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="../css/style.blue.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="../css/custom.css">
    
     
    <!-- Favicon-->
    <link rel="shortcut icon" href="../img/<?php echo $sf_row['logo']; ?>">
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
  