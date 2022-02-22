<?php

declare (strict_types = 1);

require 'Database.php';

use Firebase\JWT\JWT;

class Authentication extends Database
{
    public function create_user(string $fname, string $lname, string $username, string $password)
    {
        try {
            if ($fname && $lname) {
                //check if user already exists
                $sqlUserExists = $this->db->query("SELECT * FROM `user` WHERE `username`='$username'");
                $numRowExists = $sqlUserExists->num_rows;

                if ($numRowExists > 0) {
                    return $this->message("User exists");
                } else {
                    //insert into db
                
                    $sql = $this->db->query("INSERT INTO `user`( `fname`, `lname`,`username`,`password`, `date_created`) VALUES ('$fname','$lname','$username','$password',NOW())");
                    if ($sql) {
                        return $this->message("User created");
                    } else {
                        return $this->db->error;
                    }
                }

            } else {
                return $this->message("Invalid inputs");
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login(string $username, string $password)
    {
        try {
            if ($username && $password) {
                $sql = $this->db->query("SELECT * FROM `user` WHERE `username`='$username' AND `password` = '$password' ");

                $numrows = $sql->num_rows;

                if ($numrows > 0) {
                    $rw = $sql->fetch_assoc();
                    // takes care of the security and tokenization
                    $secret_key = '1234567890';
                    $issuer_claim = "localhost/interview"; // this can be the servername
                    $audience_claim = "THE_AUDIENCE";
                    $issuedat_claim = time(); // issued at
                    $notbefore_claim = $issuedat_claim + 10; //not before in seconds
                    $expire_claim = $issuedat_claim + 60; // expire time in seconds

                    $token = array(
                        "iss" => $issuer_claim,
                        "aud" => $audience_claim,
                        "iat" => $issuedat_claim,
                        "nbf" => $notbefore_claim,
                        "exp" => $expire_claim,
                        "data" => array(
                            "id" => $rw['id'],
                            "lname" => $rw['lname'],
                            "fname" => $rw['fname'],

                        ));

                    http_response_code(200);

                    $jwt = JWT::encode($token, $secret_key, 'HS512');
                    return json_encode(["message" => "login successful", "JWT" => $jwt]);
                } else {
                    return $this->message("incorrect username or password");
                }
            } else {
                return $this->message("Invalid input");
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create_product(int $userId, string $title, string $description, float $amount)
    {
        if ($userId && $title && $description && $amount) {
            $sql = $this->db->query("INSERT INTO `items`(`user_id`, `title`, `description`, `amt`,`deleted`, `date_created`) VALUES ($userId,'$title','$description',$amount,0,NOW())");
            if ($sql) {
                return $this->message("item created");
            }
        } else {
            return $this->db->query("Invalid inputs");
        }
    }

    public function read_products(int $id = 0)
    {
        if ($id != 0) {
            $sqlOne = $this->db->query("SELECT * FROM `items` WHERE `id` = $id AND `deleted` = 0");

            $numrows = $sqlOne->num_rows;

            if ($numrows > 0) {
                $data1 = [];
                $rw = $sqlOne->fetch_assoc();
                array_push($data1, $rw);
                return $this->out($data1);
            }

        } else {
            $sqlAll = $this->db->query("SELECT * FROM `items` WHERE `deleted`= 0 ");

            $numrowsAll = $sqlAll->num_rows;

            if ($numrowsAll > 0) {
                $data = [];
                while ($rws = $sqlAll->fetch_assoc()) {
                    array_push($data, $rws);
                }

                return $this->out($data);
            }
        }
    }

    public function delete_item(int $id = 0)
    {

        if ($id != 0) {
            $sql = $this->db->query("UPDATE `items` SET `deleted` = 1  WHERE `id` = $id");
            if ($sql) {
                return $this->message("Deleted!");
            }
        }

    }

    public function update_item(int $id = 0, string $title, string $description, float $amount)
    {
        $sql = $this->db->query("UPDATE `items` SET `title`='$title',`description`='$description',`amt`=$amount WHERE `id`=$id AND `deleted` = 0");
        if ($sql) {
            return $this->message("Updated successfully");
        }

    }
}
