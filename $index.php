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

class NameEmailTitleOfficeAndPhoneView {
    public function __construct($model) {
        $this->model = $model;
    }

    public function build() {
        $prefix = $this->model->prefix;
        $firstName = $this->model->firstName;
        $lastName = $this->model->lastName;
        $email = $this->model->email;
        $jobTitle = $this->model->jobTitle;
        $phoneNumber = $this->model->phoneNumber;
        $tagName = $this->model->tagName;
        $suffix = $this->model->suffix;
        $person = $this->model->person;
        $buildingFull = $this->model->buildingFull;
        $buildingCode = $this->model->buildingCode;
        $roomNumber = $this->model->roomNumber;
        $image = $this->model->image;
        $biography = $this->model->biography;
        $education = $this->model->education;
        $researchInterests = $this->model->researchInterests;
        $html = "";

        $name = "";
        if (strcmp($prefix, 'Dr.') == 0 && strpos($suffix, 'Ph.D.') === false)
            $name .= $prefix . ' ';
        $name .= $firstName . ' ' . $lastName;
        if (strlen($suffix) > 0)
            $name .= ', ' . $suffix;
        if (strlen($email) > 0)
            $email = "<a href='mailto:$email'><strong>$name</strong></a>";

        $jobTitle = "<br />$jobTitle";

        if (strlen($buildingCode) > 0) 
            $building = "<br />Office Location: $buildingFull ($buildingCode), Room $roomNumber";

        if ($phoneNumber > 0) 
            $phone = "<br />Phone: 575.562.$phoneNumber";


        /**** Variables ****/

        $fullImage = "<input type=\"image\" src=\"$image\" alt=\"$firstName\" height=\"200\" width=\"200\" class=\"dir-readon\" data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\">
        </div>";
        $fullName = $firstName . " " . $lastName;
        $modalName = "<p><strong>Name: </strong>$fullName</p>";
        $modalTitle = "<p><strong>Title: </strong>$jobTitle</p>";
        $modalPhone = "<p><strong>Phone: </strong>$phoneNumber</p>";
        $modalEmail = "<p><strong>Email: </strong>$email</p>";
        $descriptionJobTitle = "<p class=\"description-modal\"><strong>Title:</strong>$jobTitle</p>";
        $descriptionOffice = "<strong>Office Location:</strong>$buildingFull ($buildingCode), Room $roomNumber";
        $descriptionPhone = "<p class=\"description-modal\"><strong>Phone:</strong> $phoneNumber</p>";
         
        /**** Functions ****/

        /*Education */
        function descriptionEducation() {
            /* Creates and array of characters everytime there is a line break then prints them */
            if (strlen($row['pwbcdir_education']) > 0){
                echo '<h3>Education</h3>';
                $educationArray = explode("\n", $row['pwbcdir_education']);
                foreach ($educationArray as $item) {
                    /*if statement here checks if the line item within the array is not blank, and will post the paragraph if it is not */
                    if (strlen($item) > 0){
                    echo '<p class="p-modal">' . $item . '</p>';}
                }
            }
        }
        
        /*Biography */
        function descriptionBio(){
            if (strlen($row['pwbcdir_biography']) > 0) {
                echo '<h3>Bio</h3>';
                $bioArray = explode("\n", $row['pwbcdir_biography']);
                foreach ($bioArray as $item) {
                    if (strlen($item) > 0){
                    echo '<p>' . $item . '</p>';}
                }
            }  
        }

        /*Research Interest*/
        function descriptionResearch(){
            if (strlen($row['pwbcdir_research_interests']) > 0) {
                echo '<h3>Research Interests</h3>';
                $researchInterestsArray = explode("\n", $row['pwbcdir_research_interests']);
                foreach ($researchInterestsArray as $item) {
                    if (strlen($item) > 0){
                    echo '<p>' . $item . '</p>';}
                }
            }
        }

        /**** HTML ****/

        $html = "
            <div class=\"container\">
                <div class=\"row\">
                    <div class=\"col-sm-6\">
                        $fullImage
                        <div class=\"col-sm-6\">
                        $modalName
                        $modalTitle
                        <p>$descriptionOffice</p>
                        $modalPhone
                        $modalEmail
                        <button type=\"button\" class=\"readon\" data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\">
                        Faculty Bio 
                        </button>
                    </div>
                </div>


            <!--Modal-->
            <div class=\"modal fade\" id=\"exampleModal\" tabindex=\"-1\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog modal-xl\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                        <h1 class=\"modal-title fs-5\" id=\"exampleModalLabel\">$fullName</h1>
                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                    </div>

                        <!--Modal Body-->
                        <div class=\"modal-body\">
                            <div class=\"row align-items-top\">
                                <div class=\"col-sm-12 col-md-4\">
                                    $fullImage
                                    </div>
                                    <div class=\"col-sm-12 col-md-4\">
                                    <h3>$fullName</h3>
                                    $descriptionJobTitle
                                    <p class=\"description-modal\">$descriptionOffice</p>
                                    $descriptionPhone
                                    $modalEmail
                                </div>
                                <div class=\"col-sm-12 col-md-4\">
                                <h3>Education</h3>
                                <p class = \"p-modal\">"
                                . descriptionEducation() . 
                                
                                "</div>
                                <div class=\"row\">
                                    <div class=\"col-sm-12 md-4\">
                                        <h3>Bio</h3>
                                        " . descriptionBio() . "
                                        <h3>Research Interest</h3>
                                        " . descriptionResearch() . "
                                    </div>
                                </div>
                            </div>
                            <!--Modal Footer-->
                            <div class=\"modal-footer\">
                            <button type=\"button\" class=\"readon\" data-bs-dismiss=\"modal\">Close</button>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
                    
                    ";
        return $html;
    }
}
?>
