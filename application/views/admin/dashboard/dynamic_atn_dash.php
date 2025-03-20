
<?php $this->load->view('admin/components/htmlheader');?>
<body class="hrsale-layout hold-transition sidebar-mini skin-blue">
    <div class="wrapper">
        <?php $this->load->view('admin/components/header2');?>
        <!-- Main /.content -->
        <section class="content" style="height: 85vh">
    	    <?php $this->load->view('admin/dashboard/dynamic_attendance'); ?>
        </section>
        <!-- Main /.content -->

        <!-- footer section -->
        <?php $system = $this->Xin_model->read_setting_info(1);?>
        <footer class="main-footer footer-dark" style="margin-left: 0px !important;">
            <strong>
                <?php echo date('Y');?> © <b><?php echo $system[0]->footer_text;?> <?php echo $this->Xin_model->hrsale_version();?></b>
            </strong>
        </footer>
        <!-- footer section end -->
        <?php $this->load->view('admin/components/htmlfooter');?>
    </div>
</body>
</html>
