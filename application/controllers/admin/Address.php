<?php

/**
* NOTICE OF LICENSE
*
* This source file is subject to the HRSALE License
* that is bundled with this package in the file license.txt.
* It is also available through the world-wide-web at this URL:
* http://www.hrsale.com/license.txt
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to hrsalesoft@gmail.com so we can send you a copy immediately.
*
* @author   HRSALE
* @author-email  hrsalesoft@gmail.com
* @copyright  Copyright © hrsale.com. All Rights Reserved
*/
defined('BASEPATH') or exit('No direct script access allowed');

class Address extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->model('Xin_model');
    }
    ////// Division Start
    public function division()
    {
        $data['title'] = 'Division | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='Division';
        $data['divisions'] = $this->db->select('*')->from('emp_divisions')->get()->result();
        $data['subview'] = $this->load->view("admin/division/index", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function add_division()
    {
        if ($this->input->post('name_en')) {
            $data = array(
                'name_en' => $this->input->post('name_en'),
            );
            $this->db->insert('emp_divisions', $data);
            redirect('admin/address/division/');
        }
        $data['title'] = 'Division Add | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='Division Add';
        $data['subview'] = $this->load->view("admin/division/create", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function edit_division($division_id)
    {
        if ($this->input->post('name_en')) {
            $data = array(
                'name_en' => $this->input->post('name_en'),
            );
            $this->db->where('id', $division_id);
            $this->db->update('emp_divisions', $data);
            redirect('admin/address/division/');
        }
        $this->db->where('id', $division_id);
        $data['division'] = $this->db->get('emp_divisions')->row();
        $data['division_id'] = $division_id;
        $data['title'] = 'Division Add | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='Division Add';
        $data['subview'] = $this->load->view("admin/division/edit", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function delete_division($division_id)
    {
        $this->db->where('id', $division_id);
        $this->db->delete('emp_divisions');
        redirect('admin/address/division/');
    }
    //////   Division  end /////



    ////// district Start
    public function district()
    {
        $data['title'] = 'District | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='District';
        $this->db->select('d.id,d.name_en,dv.name_en as division_name');
        $this->db->from('emp_districts d');
        $this->db->join('emp_divisions dv', 'd.div_id = dv.id', 'LEFT');
        $data['districts'] = $this->db->get()->result();
        $data['subview'] = $this->load->view("admin/district/index", $data, true);
        $this->load->view('admin/layout/layout_main', $data); 
    }
    public function add_district()
    {
        if ($this->input->post('name_en')) {
            $data = array(
                'name_en' => $this->input->post('name_en'),
                'div_id' => $this->input->post('div_id'),
            );
            $this->db->insert('emp_districts', $data);
            redirect('admin/address/district/');
        }
        $data['divisions'] = $this->db->select('*')->from('emp_divisions')->get()->result();
        $data['title'] = 'District Add | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='District Add';
        $data['subview'] = $this->load->view("admin/district/create", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function edit_district($district_id)
    {
        if ($this->input->post('name_en')) {
            $data = array(
                'name_en' => $this->input->post('name_en'),
                'div_id' => $this->input->post('div_id'),
            );
            $this->db->where('id', $district_id);
            $this->db->update('emp_districts', $data);
            redirect('admin/address/district/');
        }
        $this->db->where('id', $district_id);
        $data['district'] = $this->db->get('emp_districts')->row();
        $data['divisions'] = $this->db->select('*')->from('emp_divisions')->get()->result();

        $data['district_id'] = $district_id;
        $data['title'] = 'district Add | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='district Add';
        $data['subview'] = $this->load->view("admin/district/edit", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function delete_district($district_id)
    {
        $this->db->where('id', $district_id);
        $this->db->delete('emp_districts');
        redirect('admin/address/district/');
    }
    //////   District  end /////
    ////// upazila Start
    public function upazila()
    {
        $data['title'] = 'upazila | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='upazila';
        $this->db->select('u.id,u.name_en,dv.name_en as division_name,d.name_en as district_name');
        $this->db->from('emp_upazilas u');
        $this->db->join('emp_districts d', 'u.dis_id = d.id', 'LEFT');
        $this->db->join('emp_divisions dv', 'd.div_id = dv.id', 'LEFT');
        $data['upazilas'] = $this->db->get()->result();
        $data['subview'] = $this->load->view("admin/upazila/index", $data, true);
        $this->load->view('admin/layout/layout_main', $data); 
    }
    public function add_upazila()
    {
        if ($this->input->post('name_en')) {
            $data = array(
                'name_en' => $this->input->post('name_en'),
                'div_id' => $this->input->post('div_id'),
                'dis_id' => $this->input->post('dis_id'),

            );
            $this->db->insert('emp_upazilas', $data);
            redirect('admin/address/upazila/');
        }
        $data['divisions'] = $this->db->select('*')->from('emp_divisions')->get()->result();
        $data['title'] = 'upazila Add | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='upazila Add';
        $data['subview'] = $this->load->view("admin/upazila/create", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function edit_upazila($upazila_id)
    {
        if ($this->input->post('name_en')) {
            $data = array(
                'name_en' => $this->input->post('name_en'),
                'div_id' => $this->input->post('div_id'),
            );
            $this->db->where('id', $upazila_id);
            $this->db->update('emp_upazilas', $data);
            redirect('admin/address/upazila/');
        }
        $this->db->where('id', $upazila_id);
        $data['upazila'] = $this->db->get('emp_upazilas')->row();
        $data['divisions'] = $this->db->select('*')->from('emp_divisions')->get()->result();
        $data['upazila_id'] = $upazila_id;
        $data['title'] = 'upazila Add | ' . $this->Xin_model->site_title();
        $data['breadcrumbs'] ='upazila Add';
        $data['subview'] = $this->load->view("admin/upazila/edit", $data, true);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }
    public function delete_upazila($upazila_id)
    {
        $this->db->where('id', $upazila_id);
        $this->db->delete('emp_upazilas');
        redirect('admin/address/upazila/');
    }
    //////   upazila  end /////

}