<?php

class File extends MY_Controller {

	public function index() {
	}

    public function avatar() {
        if (!empty($_FILES)) {
		    $content = $_FILES['dd']['tmp_name'];
            $filename = basename($_FILES['dd']['name']);
            $response = $this->tools->post(self::PASS_URL . 'images/avatar/upload', array(
                'token' => $this->token,
                'dd' => '@' . $content . ';filename=' . $filename,
            ));
            var_dump(self::PASS_URL . 'images/avatar/upload');
            var_dump(array(
                'token' => $this->token,
                'dd' => '@' . $content . ';filename=' . $filename,
            ));
            var_dump($response); exit;
            $pagesUrl = dirname(dirname(BASEPATH)) . '/pages_web/';
            $originName = $_FILES['avatar_file']['name'];
            list($imgName, $extName) = explode('.', $originName);
            $relativeFile = 'file/avatar/' . $this->uid . '.' . $extName;
            $filePath = $pagesUrl . $relativeFile;
		    move_uploaded_file($content, $filePath);
            echo $relativeFile;
        } else {
        }
    }


}
