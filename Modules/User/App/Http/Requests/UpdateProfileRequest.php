<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = auth('user')->id();
        $rules =  [
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:15', 'unique:users,phone,' . $userId],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'gender' => [auth('user')->user()->hasRole('Teacher') ? 'nullable' : 'prohibited', 'string', 'in:m,f'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $userId],
        ];
        if (auth('user')->user()->hasRole('Student')) {
            $rules['identity_number'] = ['nullable', 'string', 'unique:students,identity_number'];
            $rules['parent_email'] = ['nullable', 'email', 'unique:students,parent_email', 'unique:students,email', 'different:email'];
            $rules['parent_phone'] = ['nullable', 'string'];
            $rules['application_form'] = ['nullable', 'file', 'mimes:pdf', 'max:1024'];
            $rules['address'] = ['nullable', 'string'];
            $rules['state_id'] = ['nullable', 'exists:states,id'];
            $rules['region'] = ['nullable', 'string'];
            $rules['branch_id'] = ['nullable', 'exists:branches,id'];
            $rules['name_en'] = ['nullable', 'string'];
            $rules['birth_date'] = ['nullable', 'date'];
            $rules['education_level'] = ['nullable', 'in:excellent,normal,needs_follow_up'];
            $rules['has_learning_difficulties'] = ['nullable', 'boolean'];
            $rules['educational_system'] = ['nullable', 'in:monolingual,bilingual'];
            $rules['behavioral_data'] = ['nullable', 'array'];
            $rules['behavioral_data.*'] = ['nullable', 'string', 'in:articulate,sociable,introvert,shy,unresponsive,inflexible,irritable,hyperactive,stubbon,normal,aggressive'];
            $rules['pronunciation'] = ['nullable', 'in:excellent,good,needs_follow_up'];
            $rules['chronic_diseases'] = ['nullable', 'string'];
            $rules['food_allergies'] = ['nullable', 'string'];
            $rules['other_notes'] = ['nullable', 'string'];
            $rules['transport'] = ['nullable', 'in:school_bus,private_bus'];
            $rules['street_number'] = ['nullable', 'string'];
            $rules['house_number'] = ['nullable', 'string'];
            $rules['nearest_landmark'] = ['nullable', 'string'];
            $rules['home_location_url'] = ['nullable', 'string', 'url'];
            $rules['siblings_count'] = ['nullable', 'string'];
            $rules['parent_identity_card_image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['student_residence_card_image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['student_passport_image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['student_birth_certificate_image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['student_health_card_image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['home_map_image'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'];
            $rules['parent_name'] = ['nullable', 'string'];
            $rules['parent_nationality'] = ['nullable', 'string'];
            $rules['parent_identity_number'] = ['nullable', 'string'];
            $rules['parent_job'] = ['nullable', 'string'];
            $rules['parent_job_address'] = ['nullable', 'string'];
            $rules['parent_education_level'] = ['nullable', 'string'];
            $rules['mother_name'] = ['nullable', 'string'];
            $rules['mother_nationality'] = ['nullable', 'string'];
            $rules['mother_identity_number'] = ['nullable', 'string'];
            $rules['mother_job'] = ['nullable', 'string'];
            $rules['mother_job_address'] = ['nullable', 'string'];
            $rules['mother_education_level'] = ['nullable', 'string'];
            $rules['mother_phone'] = ['nullable', 'string'];
            $rules['parents_status'] = ['nullable', 'in:together,separated,widower,widow,father_dead,mother_dead'];
            $rules['relative_name'] = ['nullable', 'string'];
            $rules['relative_relation'] = ['nullable', 'string'];
            $rules['relative_phone'] = ['nullable', 'string'];
            $rules['distinguished_skills'] = ['nullable', 'string'];
            $rules['has_previous_education'] = ['nullable', 'boolean'];
            $rules['previous_school_data'] = ['nullable', 'string'];
            $rules['can_distinguish_letters_randomly'] = ['nullable', 'boolean'];
            $rules['reads_short_words'] = ['nullable', 'in:excellent,very_good,good,cannot_read'];
            $rules['reads_short_sentences'] = ['nullable', 'in:excellent,very_good,good,cannot_read'];
            $rules['memorizes_quran_surahs'] = ['nullable', 'boolean'];
            $rules['memorizes_quran_from'] = ['nullable', 'string'];
            $rules['memorizes_quran_to'] = ['nullable', 'string'];
            $rules['additional_educational_notes'] = ['nullable', 'string'];
            $rules['sibling_order'] = ['nullable', 'string'];
        }
        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'phone' => 'Phone',
            'image' => 'Image',
            'gender' => 'Gender',
            'email' => 'Email',
            'parent_email' => 'Parent Email',
            'parent_phone' => 'Parent Phone',
            'application_form' => 'Application Form',
            'address' => 'Address',
            'state_id' => 'State',
            'branch_id' => 'Branch',
            'name_en' => 'Name in English',
            'birth_date' => 'Birth Date',
            'education_level' => 'Education Level',
            'has_learning_difficulties' => 'Has Learning Difficulties',
            'educational_system' => 'Educational System',
            'behavioral_data' => 'Behavioral Data',
            'pronunciation' => 'Pronunciation',
            'chronic_diseases' => 'Chronic Diseases',
            'food_allergies' => 'Food Allergies',
            'other_notes' => 'Other Notes',
            'transport' => 'Transport',
            'street_number' => 'Street Number',
            'house_number' => 'House Number',
            'nearest_landmark' => 'Nearest Landmark',
            'home_location_url' => 'Home Location URL',
            'siblings_count' => 'Siblings Count',
            'parent_identity_card_image' => 'Parent Identity Card Image',
            'student_residence_card_image' => 'Student Residence Card Image',
            'student_passport_image' => 'Student Passport Image',
            'student_birth_certificate_image' => 'Student Birth Certificate Image',
            'student_health_card_image' => 'Student Health Card Image',
            'home_map_image' => 'Home Map Image',
            'parent_name' => 'Parent Name',
            'parent_nationality' => 'Parent Nationality',
            'parent_identity_number' => 'Parent Identity Number',
            'parent_job' => 'Parent Job',
            'parent_job_address' => 'Parent Job Address',
            'parent_education_level' => 'Parent Education Level',
            'mother_name' => 'Mother Name',
            'mother_nationality' => 'Mother Nationality',
            'mother_identity_number' => 'Mother Identity Number',
            'mother_job' => 'Mother Job',
            'mother_job_address' => 'Mother Job Address',
            'mother_education_level' => 'Mother Education Level',
            'mother_phone' => 'Mother Phone',
            'parents_status' => 'Parents Status',
            'relative_name' => 'Relative Name',
            'relative_relation' => 'Relative Relation',
            'relative_phone' => 'Relative Phone',
            'distinguished_skills' => 'Distinguished Skills',
            'has_previous_education' => 'Has Previous Education',
            'previous_school_data' => 'Previous School Data',
            'can_distinguish_letters_randomly' => 'Can Distinguish Letters Randomly',
            'reads_short_words' => 'Reads Short Words',
            'reads_short_sentences' => 'Reads Short Sentences',
            'memorizes_quran_surahs' => 'Memorizes Quran Surahs',
            'memorizes_quran_from' => 'Memorizes Quran From',
            'memorizes_quran_to' => 'Memorizes Quran To',
            'additional_educational_notes' => 'Additional Educational Notes',
            'sibling_order' => 'Sibling Order',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                trans('validation.rules_failed'),
                $validator->errors()->messages(),
                'unprocessable_entity'
            )
        );
    }
}
