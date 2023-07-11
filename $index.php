<?php
class NameEmailTitleOfficeAndPhoneModel {
    public function __construct($db, $id = null, $name = null) {
        $query = $db->getQuery(true);
        
        $query
            ->select(array(
                "pidm",
                "building",
                "room_numb",
                "image",
                "phone",
                "title",
                "url",
                "first_name",
                "last_name",
                "email_address",
                "name_prefix",
                "tag_desc",
                "biography",
                "education",
                "pwbcdir_research_interests",
                "pwbtags_tag_code" 
            ))
            ->from("enmu ")
            ->join("LEFT OUTER", "enmu.tags ON dep_dept_index = tags_dept_index")
            ->join("LEFT OUTER", "enmu.spri ON tags_cont_pidm = spri_pidm")
            ->join("LEFT OUTER", "enmu.spb ON tags_cont_pidm = spb_pidm")
            ->join("LEFT OUTER", "enmu.dir ON tags_cont_pidm = dir_pidm")
            ->join("LEFT OUTER", "enmu.go ON dir_pidm = go_pidm ")
            ->where("tags_tag_code = $id");
        $db->setQuery($query);

        $row = $db->loadAssoc();
    
        $this->prefix = $row['spb_name_prefix'];
        $this->firstName = $row['spri_first_name'];
        $this->lastName = $row['spri_last_name'];
        $this->jobTitle = $row['dir_title'];
        $this->buildingCode = $row['dir_building'];
        $this->roomNumber = $row['dir_room_numb'];
        $this->phoneNumber = $row['dir_phone'];
        $this->tagName = $row['tags_tag_desc'];
        $this->suffix = $row['dir_suffix'];
        $this->person = $row['tags_person_ind'];
        $this->image = $row['dir_image'];
        $this->email = $row['go_email_address'];

        if (strlen($name) > 0)
            $this->email = $name;
        else 
            $this->email = $row['go_email_address'];
    
        if (strlen($this->buildingCode) > 0) {
            $building_query = $db->getQuery(true);
            $building_query
                ->select("buildings_full_name")
                ->from("enmu.buildings")
                ->where("buildings_code = '{$this->buildingCode}'");
            $db->setQuery($building_query);
            $buildingRow = $db->loadAssoc();
            $this->buildingFull = $buildingRow['buildings_full_name'];
        }
    }
}

    echo 'class="row"';
    echo '<div class = "col-sm-6">';
    echo '<input type="image" src="' . $image . 'alt =' . $firstName . $lastName .'height="200" width="200" class="dir-readon" data-bs-toggle="modal" data-bs-target="#exampleModal">';
    echo '</div>';
    echo '<div class="col-sm-6">';
    echo '<p><strong>Name:</strong>' . $firstName . $lastName . '</p>';
    echo '<p><strong>Title:</strong>' . $jobTitle . '</p>';
    echo '<p><strong>Office:</strong>' . $buildingCode . $roomNumber . '</p>';
    echo '<p><strong>Phone:</strong>' . $phoneNumber . '<p>';
    echo '<p><strong>Email:</strong> <a href="' . $email . '"' . $email . '</a></p>';
    echo '<button type="button" class="readon" data-bs-toggle="modal">data-bs-target="#exampleModal"> Faculty Bio </button>';
    echo '</div>';
    echo '</div>';

    /*Modal*/

    echo '<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">';
    echo '<div class="modal-dialog modal-xl">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h1 class="modal-title" id="exampleModalLabel"><h1>';
    echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
    echo '</div>';

    /* Modal Body */

    echo '<div class="modal-body">';
    echo '<div class="row align-items-top">';
    echo '<div class="col-sm-12 col-md-4">';
    echo '<img src="' . $image . '" alt="doggo" height="200" width="200"/>';
    echo '</div>';
    echo '<div class="col-sm-12 col-md-4">';
    echo '<h3>' . $firstName . $lastName . '</h3>';
    echo '<p class="description-modal"><strong>Title:</strong>' . $jobTitle . '</p>';
    echo '<p class="description-modal"><strong>Office Location:</strong>' . $buildingCode . $roomNumber . '</p>';
    echo '<p class="description-modal"><strong>Phone:</strong>' . $phoneNumber / '</p>';
    echo '<strong>Email: </strong><a href="mailto: ' . $email . '"> ' . $email . '</a>';
    echo '</div>';
    echo '<div class="col-sm-12 col-md-4">';
  
    /* For Education Creates and array of characters everytime there is a line break then prints them */
    if (strlen($row['pwbcdir_education']) > 0){
        echo '<h3>Education</h3>';
        $educationArray = explode("\n", $row['pwbcdir_education']);
        foreach ($educationArray as $item) {
            /*if statement here checks if the line item within the array is not blank, and will post the paragraph if it is not */
            if (strlen($item) > 0){
            echo '<p class="p-modal">' . $item . '</p>';}
            }
        }

        echo '</div>';
        echo '<div class="row">';
        echo '<div class="col-sm-12 md-4">';

        /*Bio*/
        if (strlen($row['pwbcdir_biography']) > 0) {
            echo '<h3>Bio</h3>';
            $bioArray = explode("\n", $row['pwbcdir_biography']);
            foreach ($bioArray as $item) {
                if (strlen($item) > 0){
                echo '<p>' . $item . '</p>';}
            }
        }

        /*Research Interest */
        if (strlen($row['pwbcdir_research_interests']) > 0) {
            echo '<h3>Research Interests</h3>';
            $researchInterestsArray = explode("\n", $row['pwbcdir_research_interests']);
            foreach ($researchInterestsArray as $item) {
                if (strlen($item) > 0){
                echo '<p>' . $item . '</p>';}
            }
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        /*End of Modal Body */
        echo '<div class="modal-footer">';
        echo '<button type="button" class="readon" data-bs-dismiss="modal">Close</button>';
        echo '</div>';
        /* End of modal */
        echo '</div>';
        echo '</div>';
        echo '</div>';
?>
