<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;

class UploadController{

	private $role;
	/* private $title=null;
	private $description=null; */
	private $messages=[];
	private $lastUploadedId;

	private $placeholder=[
		'title'=>'required',
		'description'=>'required',
	];

	public function __construct(){
        $user=Auth::user();
        $userId=Auth::user()[0]->id;
        $this->role = App::get('role');
		$this->role -> getRole($userId);	
		$_SESSION['sim-messages'] =[];
    }

	//index page of upload menu
	public function index(){
		view('upload/index');
	}

	public function upload(){
		if(!$this->role->can("upload-data")){
            redirectWithMessage(["Anda tidak memiliki hak untuk melakukan upload data", 0],'/');
        }

		//checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        
		foreach($this->placeholder as $k => $v){
			if(checkRequirement($v, $k, $_POST[$k])){
				$data[$k]=filterUserInput($_POST[$k]);
			}else{
				$passingRequirement=false;
			}  
		}

		$this->title=$data['title'];
		$this->description=$data['description'];

		//dd($data);

		if($passingRequirement){
			$upload = $this->processingUpload($_FILES["upload_file"]);

			//if fail to upload
			if(!$upload){
				//redirectWithMessage([['Data tidak memenuhi persyaratan, mohon diisi data yang diperlukan',0]], '/');
				//redirect(getLastVisitedPage());
				//exit();
				redirectWithMessage([['Gagal upload file', 0 ]], getLastVisitedPage());
			}
			redirectWithMessage([['Berhasil upload file', 1 ]], getLastVisitedPage());

		}else{
			redirectWithMessage([['Gagal upload file. Persyaratan tidak terpenuhi', 0 ]], getLastVisitedPage());
		}

	}

	//make upload data
	public function processingUpload($upload_file, $allowFileType=null, $title=null, $description=null){

		$createdBy = substr($_SESSION['sim-id'], 3, -3);

		//whether this upload file is marked as private
		if(isset($_POST['private']) && $_POST['private']==1){
			$public = 0;
		}else{
			$public = 1;
		}

		//upload..thanks to w3school
		
		if($upload_file["name"]!=""){
		
			$target_dir =$_SERVER['DOCUMENT_ROOT']."/public/upload/";
			$target_file = time().basename($upload_file["name"]);

			$target_file= str_replace(" ", "_", $target_file);
			
			$uploadOk = 1;
			$uploadFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	
			// Check if image file is a actual image or fake image			
			$check = getimagesize($upload_file["tmp_name"]);

			if($check !== false) {
				$msg= "File is an image - " . $check["mime"] . ".";
				//file_type 1 ->image
				//file_type 2 ->audio
				//file_type 3 ->video
				$file_type=1;
				//$uploadOk = 1;
			} else {
				// check apakah type video
				//die(pathinfo($target_file,PATHINFO_EXTENSION));
				if(strstr($upload_file['type'],"audio")){
					$file_type=2;
					//$uploadOk = 1;
				}elseif (strstr($upload_file['type'],"video")){
					$file_type=3;
					//$uploadOk = 1;
				}elseif(strstr($upload_file['type'],"application")){
					$file_type=4;
					//$uploadOk = 1;
				}else{

					
					
					$msg= ["File bukan merupakan gambar, video atau audio", 0];
					array_push($_SESSION['sim-messages'] ,$msg);
					
					$uploadOk = 0;
				}
			}

			// Check if file already exists
			if (file_exists($target_file)) {
				$msg= ["Maaf, file tersebut sudah ada",0];
				array_push($_SESSION['sim-messages'] ,$msg);
				$uploadOk = 0;
			}
				
			// Check file size
			// maksimal 10 MB
			// 1 MB=1024KB=10485760 B
			if ($upload_file["size"] > 10485760) {
				$msg= ['Maaf, ukuran file tersebut melebihi batas. Maksimal 10 MB',0];
				array_push($_SESSION['sim-messages'] ,$msg);
				$uploadOk = 0;
			}

			$fileTypeCategory=[1=>['jpg', 'png', 'jpeg', 'gif'], 2=>['mp3'], 3=>['mp4','mov'], 4=>['pdf']];

			if($allowFileType!=null){
				if($allowFileType!=$file_type){
					$e=implode(', ', $fileTypeCategory[$allowFileType]);
					$msg= ["Maaf, hanya file $e yang diijinkan", 0];
					array_push($_SESSION['sim-messages'] ,$msg);

					return false;
				}
			}

			if(array_key_exists($file_type, $fileTypeCategory)){
				$uploadOk=0;

				for($i=0; $i<count($fileTypeCategory[$file_type]); $i++){
					if($uploadFileType==$fileTypeCategory[$file_type][$i]){
						$uploadOk = 1;
					}
				}

				if($uploadOk==0){
					$e=implode(',', $fileTypeCategory[$file_type]);
					$msg= ["Maaf, hanya file $e yang diijinkan", 0];
					array_push($_SESSION['sim-messages'] ,$msg);
				}
			}
			
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				$msg= ["Maaf, File tersebut tidak dapat diunggah. Terdapat error.", 0];
				array_push($_SESSION['sim-messages'] ,$msg);

			} else {
				
				//insert upload data to database
				//if success then move the file to specified folder
				$builder=App::get('builder');

				$insertToUploadFile=$builder->insert('upload_files',[
					'upload_file'=>$target_file,
					'title'=>$title,
					'description'=>$description,
					'public'=>$public,
					'file_type'=>$file_type,
					'created_by'=>$createdBy
				]);

				if($insertToUploadFile){
					$msg=["File berhasil disimpan", 1];
					array_push($_SESSION['sim-messages'] ,$msg);

					if (move_uploaded_file($upload_file["tmp_name"],$target_dir.$target_file)) {
					 
						$msg= ["Sukses upload data",1];
						array_push($_SESSION['sim-messages'] ,$msg);

						$uploadSuccess=true;

					} else {
						$msg= ["Maaf, terjadi kesalahan ketika mengunggah, mohon ulangi lagi",0];
						
						array_push($_SESSION['sim-messages'] ,$msg);

						$uploadSuccess=false;

						$builder->cancel();

						return $uploadSuccess;
					}
					
					$this->lastUploadedId=$builder->getPdo()->lastInsertId();

					//$builder->save();
				}else{
					$msg= ["Maaf, terjadi kesalahan ketika mengunggah, mohon ulangi lagi",0];
					
					array_push($_SESSION['sim-messages'] ,$msg);

					$uploadSuccess=false;

					$builder->cancel();

					return $uploadSuccess;
				}
			}
		}else{
			$uploadSuccess=false;
		}

		return $uploadSuccess;

	}

	//remove uploaded data
	public function remove(){
		if(!$this->role->can("remove-upload")){
            redirectWithMessage(["Anda tidak memiliki hak untuk menghapus data upload", 0],getLastVisitedPage());
        }

		if(isEmpty($_POST['id'])){
			redirectWithMessage([["Terdapat problem, coba lagi",1]],getLastVisitedPage());
		}

		$id=filterUserInput($_POST['id']);
		
		$builder=App::get('builder');

		$deleteData=$builder->delete('upload_files', ['id'=>$id], '' , 'Document' );

		recordLog('Upload data', "Upload data berhasil");
        
		$builder->save();

		//redirect to form page with message
        redirectWithMessage([["Upload data berhasil",1]],getLastVisitedPage());

	}

	public function getLastUploadedId(){
		return $this->lastUploadedId;
	}

	//background
	public function set_background($menu, $bg){
		$sql="select * from background where menu=$menu";
		parent::db_query($sql);
		$result=$this->get_result();
		if($this->num_rows()>0){
			$sql="update background set background=$bg, updated_at=now(), updated_by=$_SESSION[id] where menu=$menu";
		}else{
			$sql="insert into background(`menu`, `background`, `created_at`, `created_by`, `updated_at`, `updated_by`) values($menu, $bg, now(), $_SESSION[id], now(), $_SESSION[id])";
		}
		parent::db_query($sql);
		$result=$this->get_result();
		if($result){
			return "Pengaturan background berhasil";
		}else{
			return "Pengaturan background gagal";
		}
	}

	public function get_background($menu){
		$sql="select b.file as image from background as a inner join upload_files as b on a.background=b.id where a.menu=$menu and a.active=1";
		parent::db_query($sql);
		$result=$this->get_result();
		$row=mysqli_fetch_assoc($result);
		return $row['image'];
	}

	public function toggle_background($menu){
		$sql="update background set active=CASE active when 0 THEN 1 ELSE 0 END where menu=$menu";
		parent::db_query($sql);
		$result=$this->get_result();
		if($result){
			return "Berhasil update background";
		}else{
			return "Gagal update background";
		}
	}
	
	public function raw_command($sql){
		parent::db_query($sql);
		$result=$this->get_result();
		return $result;
	}
}

?>