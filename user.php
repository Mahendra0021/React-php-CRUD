<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
header("Access-Control-Allow-Origin:* ");
header("Access-Control-Allow-Headers:* ");
header("Access-Control-Allow-Methods:* ");
$db_conn = mysqli_connect("localhost", "root", "M@hendr@0021","reactphp");
if($db_conn === false){
  die("ERROR: Could Not Connect".mysqli_connect_error());
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
  case "GET":
    $path = explode('/', $_SERVER['REQUEST_URI']);
    if (isset($path[4]) && is_numeric($path[4])) {
      $json_array = array(); 
      $id = $path[4];
      // echo "get user id........." . $id; 
      $getuserrow = mysqli_query($db_conn, "SELECT * FROM tbl_userr WHERE id='$id' ");
      while($userrow = mysqli_fetch_array($getuserrow)){
        $json_array['rowUserdata'] = array('id'=>$userrow['id'], 'name'=>$userrow['name'], 'email'=>$userrow['email'], 'password'=>$userrow['password'] );
      }
      echo json_encode($json_array['rowUserdata']);
      return;
    
    } else {
      // Fetch all users
      $alluser = mysqli_query($db_conn, "SELECT * FROM tbl_userr");
      if (mysqli_num_rows($alluser) > 0) {
        while ($row = mysqli_fetch_array($alluser)) {
          $json_array["userdata"][] = array(
            "id" => $row["id"], 
            "name" => $row["name"], 
            "email" => $row["email"], 
            "password" => $row["password"]
          );
        }
        echo json_encode($json_array["userdata"]);
        return;
      } else {
        echo json_encode(["result" => "Please check the Data"]);
        return;
      }
    }
    break;

  case "POST":
    $userpostdata = json_decode(file_get_contents("php://input"));
    $name = $userpostdata->name;
    $email = $userpostdata->email;   
    $password = $userpostdata->password;
    $result = mysqli_query($db_conn, "INSERT INTO tbl_userr (name, email, password) VALUES('$name','$email','$password')");
    if ($result) {
      echo json_encode(["success" => "User Added Successfully"]);
      return;
    } else {
      echo json_encode(["success" => "Please Check the user Data"]);
      return;
    }
    break;
    case "PUT":
      $userUpdate = json_decode(file_get_contents("php://input"));
      $id = $userUpdate->id;
      $name = $userUpdate->name;
      $email = $userUpdate->email;
      $password = $userUpdate->password;
      $userUpdate = mysqli_query($db_conn, "UPDATE tbl_userr SET name='$name', email='$email', password='$password' WHERE id='$id' ");
      if ($userUpdate) {
        echo json_encode(["success" => "User Updated  Successfully"]);
        return;
      } else {
        echo json_encode(["success" => "Please Check the user Data"]);
        return;
      }
      // print_r($userUpdate);
      // die;
      break;

      case "DELETE":
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[4]) && is_numeric($path[4]))
         {
            $id = $path[4];
            $result = mysqli_query($db_conn, "DELETE FROM tbl_userr WHERE id='$id'");
            if ($result)
             {
                echo json_encode(["success" => "User deleted successfully."]);
              } 
            else{
                echo json_encode(["error" => "Failed to delete user."]);
                }
        } 
        return; 
        break;

}
?>
