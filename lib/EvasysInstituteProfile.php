<?php

class EvasysInstituteProfile extends SimpleORMap {

    protected static function configure($config = array())
    {
        $config['db_table'] = 'evasys_institute_profiles';
        $config['belongs_to']['institute'] = array(
            'class_name'  => 'Institute',
            'foreign_key' => 'institut_id'
        );
        $config['belongs_to']['semester'] = array(
            'class_name' => 'Semester',
            'foreign_key' => 'semester_id'
        );
        parent::configure($config);
    }

    static public function findByInstitute($institut_id)
    {
        $semester = Semester::findCurrent(); //findNext ?
        $profile = self::findOneBySQL("institut_id = ? AND semester_id = ?", array(
            $institut_id,
            $semester->getId()
        ));
        if (!$profile) {
            $profile = new EvasysInstituteProfile();
            $profile['institut_id'] = $institut_id;
            $profile['semester_id'] = $semester->getId();
        }
        return $profile;
    }

    public function getParentsDefaultValue($field)
    {
        if ($this->institute && !$this->institute->isFaculty()) {
            $profile = self::findByInstitute($this->institute['fakultaets_id']);
            return $profile[$field] ?: $profile->getParentsDefaultValue($field);
        } else {
            $profile = EvasysGlobalProfile::findCurrent();
            return $profile[$field];
        }
    }

    public function copyToNewSemester($semester_id)
    {
        $new_profile = new EvasysInstituteProfile();
        $new_profile->setData($this->toArray());
        $new_profile->setId($new_profile->getNewId());
        $new_profile['semester_id'] = $semester_id;
        $new_profile['user_id'] = $GLOBALS['user']->id;
        $new_profile['mkdate'] = time();
        $new_profile['chdate'] = time();
        $new_profile->store();

        $semtypeforms = EvasysProfileSemtypeForm::findBySQL("profile_id = ? profile_type = 'institute'", array($this->getId()));
        foreach ($semtypeforms as $semtypeform) {
            $new_semtypeform = new EvasysProfileSemtypeForm();
            $new_semtypeform->setData($semtypeform->toArray());
            $new_semtypeform->setId($new_semtypeform->getNewId());
            $new_semtypeform['profile_id'] = $new_profile->getId();
            $new_semtypeform['mkdate'] = time();
            $new_semtypeform['chdate'] = time();
            $new_semtypeform->store();
        }

        return $new_profile;
    }
}