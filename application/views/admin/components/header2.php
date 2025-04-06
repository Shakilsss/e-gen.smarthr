<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>
<?php
    $session = $this->session->userdata('username');
    $system = $this->Xin_model->read_setting_info(1);
    $company_info = $this->Xin_model->read_company_setting_info(1);
?>

<style type="text/css">
    .main-header .sidebar-toggle-hrsale-chat:before {
        content: "\f0e6";
    }
    .main-header .sidebar-toggle-hrsale-quicklinks:before {
        content: "\f00a";
    }
</style>

<style>
    .boxm {
        padding: 20px;
        border: 2px solid #3F51B5;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        background-color: #F5F5F5;
        margin: 18px;
    }

    p {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    #location {
        font-weight: bold;
        color: #3F51B5;
    }

    #outtime,
    #outreason {
        font-style: italic;
    }
</style>

<header class="main-header">
    <!-- Logo -->
    <a href="<?php echo site_url('admin/dashboard/');?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
            <b><img alt="<?php echo $system[0]->application_name;?>" src="<?php echo base_url();?>uploads/logo/<?php echo $company_info[0]->logo;?>" class="brand-logo" style="width:32px;"></b>
        </span>

        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img alt="<?php echo $system[0]->application_name;?>" src="<?php echo base_url();?>uploads/logo/<?php echo $company_info[0]->logo;?>" class="brand-logo" style="width:32px;">
            <b><?php echo $system[0]->application_name;?></b>
        </span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- user -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true"
                        title="<?php echo $this->lang->line('header_my_profile');?>">
                        <i class="glyphicon glyphicon-user"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="<?php echo site_url('admin/logout');?>">
                                <i class="fa fa-power-off text-red"></i><?php echo $this->lang->line('header_sign_out');?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
