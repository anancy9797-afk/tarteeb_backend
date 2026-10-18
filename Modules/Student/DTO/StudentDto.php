<?php


namespace Modules\Student\DTO;


class StudentDto
{
    public $name;
    public $email;
    public $identity_number;
    public $parent_email;
    public $parent_phone;
    public $gender;
    public $grade_id;
    public $class_id;
    public $school_id;
    public $is_fee_paid;
    public $address;
    public $state_id;
    public $region;
    public $branch_id;
    public $name_en;
    public $birth_date;
    public $education_level;
    public $has_learning_difficulties;
    public $educational_system;
    public $behavioral_data;
    public $pronunciation;
    public $chronic_diseases;
    public $food_allergies;
    public $other_notes;
    public $transport;
    public $street_number;
    public $house_number;
    public $nearest_landmark;
    public $home_location_url;
    public $siblings_count;
    public $sibling_order;
    public $distinguished_skills;
    public $has_previous_education;
    public $previous_school_data;
    public $can_distinguish_letters_randomly;
    public $reads_short_words;
    public $reads_short_sentences;
    public $memorizes_quran_surahs;
    public $memorizes_quran_from;
    public $memorizes_quran_to;
    public $additional_educational_notes;
    public $password;

    public function __construct($request)
    {
        if ($request->get('name'))
            $this->name = $request->get('name');
        if ($request->get('email'))
            $this->email = $request->get('email');
        if ($request->get('gender'))
            $this->gender = $request->get('gender');
        if ($request->get('identity_number'))
            $this->identity_number = $request->get('identity_number');
        if ($request->get('password'))
            $this->password = $request->get('password');
        if ($request->get('parent_email'))
            $this->parent_email = $request->get('parent_email');
        if ($request->get('parent_phone'))
            $this->parent_phone = $request->get('parent_phone');
        if ($request->get('grade_id'))
            $this->grade_id = $request->get('grade_id');
        if ($request->get('class_id'))
            // $this->class_id = $request->get('class_id');
          $this->class_id = $request->get('class_id') ? (int) $request->get('class_id') : null;
        if (auth('user')->user()->hasRole('Super Admin')) {
            if ($request->get('school_id')) {
                $this->school_id = $request->get('school_id');
            }
        } else if (auth('user')->user()->hasRole('School Manager')) {
            if ($request->isMethod('POST')) {
                $this->school_id = auth('user')->user()->school_id;
            }
        }
        if ($request->get('is_fee_paid'))
            $this->is_fee_paid = $request->get('is_fee_paid');
        if ($request->get('address'))
            $this->address = $request->get('address');
        if ($request->get('state_id'))
            $this->state_id = $request->get('state_id');
        if ($request->get('region'))
            $this->region = $request->get('region');
        if ($request->get('branch_id'))
            $this->branch_id = $request->get('branch_id');
        if ($request->get('name_en'))
            $this->name_en = $request->get('name_en');
        if ($request->get('birth_date'))
            $this->birth_date = $request->get('birth_date');
        if ($request->get('education_level'))
            $this->education_level = $request->get('education_level');
        if ($request->get('has_learning_difficulties'))
            $this->has_learning_difficulties = $request->get('has_learning_difficulties');
        if ($request->get('educational_system'))
            $this->educational_system = $request->get('educational_system');
        if ($request->get('behavioral_data'))
            $this->behavioral_data = $request->get('behavioral_data');
        if ($request->get('pronunciation'))
            $this->pronunciation = $request->get('pronunciation');
        if ($request->get('chronic_diseases'))
            $this->chronic_diseases = $request->get('chronic_diseases');
        if ($request->get('food_allergies'))
            $this->food_allergies = $request->get('food_allergies');
        if ($request->get('other_notes'))
            $this->other_notes = $request->get('other_notes');
        if ($request->get('transport'))
            $this->transport = $request->get('transport');
        if ($request->get('street_number'))
            $this->street_number = $request->get('street_number');
        if ($request->get('house_number'))
            $this->house_number = $request->get('house_number');
        if ($request->get('nearest_landmark'))
            $this->nearest_landmark = $request->get('nearest_landmark');
        if ($request->get('home_location_url'))
            $this->home_location_url = $request->get('home_location_url');
        if ($request->get('siblings_count'))
            $this->siblings_count = $request->get('siblings_count');
        if ($request->get('sibling_order'))
            $this->sibling_order = $request->get('sibling_order');
        if ($request->get('distinguished_skills'))
            $this->distinguished_skills = $request->get('distinguished_skills');
        if ($request->get('has_previous_education'))
            $this->has_previous_education = $request->get('has_previous_education');
        if ($request->get('previous_school_data'))
            $this->previous_school_data = $request->get('previous_school_data');
        if ($request->get('can_distinguish_letters_randomly'))
            $this->can_distinguish_letters_randomly = $request->get('can_distinguish_letters_randomly');
        if ($request->get('reads_short_words'))
            $this->reads_short_words = $request->get('reads_short_words');
        if ($request->get('reads_short_sentences'))
            $this->reads_short_sentences = $request->get('reads_short_sentences');
        if ($request->get('memorizes_quran_surahs'))
            $this->memorizes_quran_surahs = $request->get('memorizes_quran_surahs');
        if ($request->get('memorizes_quran_from'))
            $this->memorizes_quran_from = $request->get('memorizes_quran_from');
        if ($request->get('memorizes_quran_to'))
            $this->memorizes_quran_to = $request->get('memorizes_quran_to');
        if ($request->get('additional_educational_notes'))
            $this->additional_educational_notes = $request->get('additional_educational_notes');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->name == null)
            unset($data['name']);
        if ($this->email == null)
            unset($data['email']);
        if ($this->identity_number == null)
            unset($data['identity_number']);
        if ($this->password == null)
            unset($data['password']);
        if ($this->parent_email == null)
            unset($data['parent_email']);
        if ($this->parent_phone == null)
            unset($data['parent_phone']);
        if ($this->gender == null)
            unset($data['gender']);
        if ($this->grade_id == null)
            unset($data['grade_id']);
        if ($this->class_id == null)
            unset($data['class_id']);
        if ($this->school_id == null)
            unset($data['school_id']);
        if ($this->is_fee_paid == null)
            unset($data['is_fee_paid']);
        if ($this->address == null)
            unset($data['address']);
        if ($this->state_id == null)
            unset($data['state_id']);
        if ($this->region == null)
            unset($data['region']);
        if ($this->branch_id == null)
            unset($data['branch_id']);
        if ($this->name_en == null)
            unset($data['name_en']);
        if ($this->birth_date == null)
            unset($data['birth_date']);
        if ($this->education_level == null)
            unset($data['education_level']);
        if ($this->has_learning_difficulties == null)
            unset($data['has_learning_difficulties']);
        if ($this->educational_system == null)
            unset($data['educational_system']);
        if ($this->behavioral_data == null)
            unset($data['behavioral_data']);
        if ($this->pronunciation == null)
            unset($data['pronunciation']);
        if ($this->chronic_diseases == null)
            unset($data['chronic_diseases']);
        if ($this->food_allergies == null)
            unset($data['food_allergies']);
        if ($this->other_notes == null)
            unset($data['other_notes']);
        if ($this->transport == null)
            unset($data['transport']);
        if ($this->street_number == null)
            unset($data['street_number']);
        if ($this->house_number == null)
            unset($data['house_number']);
        if ($this->nearest_landmark == null)
            unset($data['nearest_landmark']);
        if ($this->home_location_url == null)
            unset($data['home_location_url']);
        if ($this->siblings_count == null)
            unset($data['siblings_count']);
        if ($this->sibling_order == null)
            unset($data['sibling_order']);
        if ($this->distinguished_skills == null)
            unset($data['distinguished_skills']);
        if ($this->has_previous_education == null)
            unset($data['has_previous_education']);
        if ($this->previous_school_data == null)
            unset($data['previous_school_data']);
        if ($this->can_distinguish_letters_randomly == null)
            unset($data['can_distinguish_letters_randomly']);
        if ($this->reads_short_words == null)
            unset($data['reads_short_words']);
        if ($this->reads_short_sentences == null)
            unset($data['reads_short_sentences']);
        if ($this->memorizes_quran_surahs == null)
            unset($data['memorizes_quran_surahs']);
        if ($this->memorizes_quran_from == null)
            unset($data['memorizes_quran_from']);
        if ($this->memorizes_quran_to == null)
            unset($data['memorizes_quran_to']);
        if ($this->additional_educational_notes == null)
            unset($data['additional_educational_notes']);
        return $data;
    }
}
