<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	public function index()
	{
		$ceks = $this->session->userdata('username');
		$id_user = $this->session->userdata('id_user');
		if(!isset($ceks)) {
			redirect('web/login');
		}else{
            $tbl_zona = $this->db->get_where('tbl_zona',array('id_zona'=>$_SESSION['id_zona']));
            $tbl_user = $this->db->get_where('tbl_user',array('id_zona'=>$_SESSION['id_zona']));

//            echo '<pre>'; print_r($tbl_user->result()[0]);die;



			$data['user']   	 = $this->Mcrud->get_users_by_un($ceks);
			$data['users']  	 = $this->Mcrud->get_users();
			$data['nama_panjang_admin']  	 = $tbl_zona->row()->nama_panjang;
			$data['nama_lengkap']  	 = $tbl_user->row()->nama_lengkap;
			$data['zona_pemda']  	 = $tbl_zona->row()->nama_zona;


//			foreach ($tbl_user->result() as $idx=>$val){
//			    if ($_SESSION['username']==$tbl_zona->row()->nama_zona){
//
//                }
//            }

			$data['judul_web'] = "Dashboard";



			$this->load->view('users/header', $data);
			$this->load->view('users/dashboard', $data);
			$this->load->view('users/footer');
		}
	}

	//lanjutkan utk beri parameter aksi dan id pada function profile ini, yg dipanggil melalui header (dlm folder user)
	public function profile($aksi='', $id='')
	{
	    //echo hashids_decrypt($id);die;
	    //echo $id;die;
	    //echo $aksi."<br>";
	    //echo $id; die;
	    //echo $_SESSION['id_user'];die;
		$ceks = $this->session->userdata('username');
		$id_user = $this->session->userdata('id_user');
		$level = $this->session->userdata('level');
		if(!isset($ceks)) {
			redirect('web/login');
		}else{
		    if ($aksi=="se"){
		        //echo "simpan edit";die;
                $input_old_password  = htmlentities(strip_tags($this->input->post('old_password')));
                $new_password_1 	 = htmlentities(strip_tags($this->input->post('new_password_1')));
                $new_password_2 	 = htmlentities(strip_tags($this->input->post('new_password_2')));
                //ini juga kunci kesuksesan get data dari database
                //$data_lama = $this->db->get_where("tbl_user", array('id_user'=>$_SESSION['id_user']))->row();
                $data_lama = $this->db->get_where("tbl_user", array('id_user'=>$_SESSION['id_user']));
                $data_password_lama = $data_lama->result()[0]->password;

                //echo $old_password."<br>".$new_password_1."<br>".$new_password_2; die;

                $id_user = $_SESSION['id_user'];
                $nama_lengkap = $data_lama->result()[0]->nama_lengkap;
                $username = $data_lama->result()[0]->username;
                $password = $data_lama->result()[0]->password;
                $level = $data_lama->result()[0]->level;
                $id_zona = $data_lama->result()[0]->id_zona;

                $pesan = "Data Belum Berhasil Disimpan!";

                //echo $data_password_lama."<br>".$input_old_password;die;

                if($data_password_lama==crypt($input_old_password,'salt-coba')){
                    //echo "inputan pass old oleh user SAMA DENGAN old pass di DB";die;
                    if($new_password_1=='' && $new_password_2==''){
                        //echo "pass 1 dan 2 tidak di isi"; die;
                        $simpan = "y";
                        $password_to_save = $data_lama->result()[0]->password;
                    } else if($new_password_1 !='' || $new_password_2 !='') {

                        if($new_password_1 == $new_password_2){
                            //echo "password 1 dan 2 sama";die;
                            $simpan = "y";
                            $password_to_save = $new_password_1;
                        } else if ($new_password_1!=$new_password_2){
                            //echo "password 1 dan 2 tidak sama";die;
                            $simpan = "n";
                            $password_to_save = $data_lama->result()[0]->password;
                        }
                    }

                    if($simpan=="y"){
                        $data = array(
                            'id_user'=>$id_user,
                            'nama_lengkap'=>$nama_lengkap,
                            'username'=>$username,
                            'password'=>crypt($password_to_save, "salt-coba"),
                            'level'=>$level,
                            'id_zona'=>$id_zona,
                            'tgl_update'=>date('Y-m-d H:i:s'),
                        );
                        $this->db->update("tbl_user",$data,array(
                            'id_user'=>$id_user,
                        ));

                        $this->session->set_flashdata('msg',
                            '
							<div class="alert alert-success alert-dismissible" role="alert">
								 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
									 <span aria-hidden="true">&times;</span>
								 </button>
								 <strong>Sukses5 brohs!</strong> Berhasil disimpan.
							</div>
						  <br>'
                        );

                    } else if($simpan=="n"){
                        $this->session->set_flashdata('msg',
                            '
											<div class="alert alert-warning alert-dismissible" role="alert">
												 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
													 <span aria-hidden="true">&times;</span>
												 </button>
												 <strong>Gagal!</strong> '.$pesan.'.
											</div>
										 <br>'
                        );

                    }
                } else {
                    //echo "inputan pass old oleh user TIDAK SAMA DENGAN old pass di DB";die;
                    $this->session->set_flashdata('msg',
                        '
											<div class="alert alert-warning alert-dismissible" role="alert">
												 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
													 <span aria-hidden="true">&times;</span>
												 </button>
												 <strong>Gagal!</strong> '.$pesan.'.
											</div>
										 <br>'
                    );

                }
                redirect("users/profile/e/".$id);
            }
			$data['user']  			  = $this->Mcrud->get_users_by_un($ceks);
			$data['level_users']  = $this->Mcrud->get_level_users();
			$get_password = $this->db->get_where("tbl_user",array('id_user'=>$_SESSION['id_user']));
			//ini adalah kunci kesuksesan mendapat data dari database
            //echo $get_password->result()[0]->password; die;

            //$data['password_lama'] = $get_password->result()[0]->password;
			$data['judul_web'] 		= "Ganti Password Pengguna";

			$this->load->view('users/header', $data);
			$this->load->view('users/profile', $data);
			$this->load->view('users/footer');
		}
	}

    public function update_pass()
    {
        //echo "update pass route tes"; die;
        //echo $_SESSION['id_user'];die;
        $ceks = $this->session->userdata('username');
        $id_user = $this->session->userdata('id_user');
        $level = $this->session->userdata('level');
        if(!isset($ceks)) {
            redirect('web/login');
        }else{
            $new_password_1 	 = htmlentities(strip_tags($this->input->post('new_password_1')));
            $new_password_2 	 = htmlentities(strip_tags($this->input->post('new_password_2')));
            //ini juga kunci kesuksesan get data dari database
            //$data_lama = $this->db->get_where("tbl_user", array('id_user'=>$_SESSION['id_user']))->row();
            $data_lama = $this->db->get_where("tbl_user", array('id_user'=>$_SESSION['id_user']));
            //echo $data_lama->password; die;
            //echo $new_password_1."<br>".$new_password_2; die;
            //echo $new_password_1."<br>".$new_password_2; die;

            //echo $data_lama->num_rows();die;
            //echo $data_lama->result()[0]->password;die;

            $id_user = $_SESSION['id_user'];
            $nama_lengkap = $data_lama->result()[0]->nama_lengkap;
            $username = $data_lama->result()[0]->username;
            $password = $data_lama->result()[0]->password;
            $level = $data_lama->result()[0]->level;
            $id_zona = $data_lama->result()[0]->id_zona;

            $pesan = "Data Belum Berhasil Disimpan!";
            if($new_password_1=='' && $new_password_2==''){
                //echo "pass 1 dan 2 tidak di isi"; die;
                $simpan = "y";
                $password = $data_lama->result()[0]->password;
            } else if($new_password_1 !='' || $new_password_2 !='') {
                //echo "salah 1 pass 1 dan 2 telah di isi"; die;
                if($new_password_1==$new_password_2){
                    //echo "password 1 dan 2 sama";die;
                    $simpan = "y";
                    $password = $new_password_1;
                } else if ($new_password_1!=$new_password_2){
                    //echo "password 1 dan 2 tidak sama";die;
                    $simpan = "n";
                    $password = $data_lama->result()[0]->password;
                }
            }

            if($simpan=="y"){
                $data = array(
                    'id_user'=>$id_user,
                    'nama_lengkap'=>$nama_lengkap,
                    'username'=>$username,
                    'password'=>$password,
                    'level'=>$level,
                    'id_zona'=>$id_zona,
                );
                $this->db->update("tbl_user",$data,array(
                    'id_user'=>$id_user,
                ));

                $this->session->set_flashdata('msg',
                    '
							<div class="alert alert-success alert-dismissible" role="alert">
								 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
									 <span aria-hidden="true">&times;</span>
								 </button>
								 <strong>Sukses5 broh!</strong> Berhasil disimpan.
							</div>
						  <br>'
                );

            } else if($simpan=="n"){
                $this->session->set_flashdata('msg',
                    '
											<div class="alert alert-warning alert-dismissible" role="alert">
												 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
													 <span aria-hidden="true">&times;</span>
												 </button>
												 <strong>Gagal!</strong> '.$pesan.'.
											</div>
										 <br>'
                );
            }

            $data['user']  			  = $this->Mcrud->get_users_by_un($ceks);
            $data['level_users']  = $this->Mcrud->get_level_users();
            $get_password = $this->db->get_where("tbl_user",array(
                'id_user'=>$_SESSION['id_user'],
            ));
            //ini adalah kunci kesuksesan mendapat data dari database
            //echo $get_password->result()[0]->password; die;

            $data['password_lama'] = $get_password->result()[0]->password;
            $data['judul_web'] 	    = "Profile";

            $this->load->view('users/header', $data);
            $this->load->view('users/profile', $data);
            $this->load->view('users/footer');
        }
    }

}
