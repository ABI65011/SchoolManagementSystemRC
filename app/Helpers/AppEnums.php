<?php

namespace App\Helpers;

enum UserRoles: string
{
    case Super = "Super";
    case Admin = "Admin";
    case Staff = "Staff";
    case Student = "Student";
    case Parent = "Parent";
    case User = "User";
    case HR = "HR";
}

enum Gender: string
{
    case Male = "male";
    case Female = "female";
}

enum ApplyingSection: string
{
    case Day = "day";
    case Boarding = "boarding";
}

enum IDType: string
{
    case NationalID = "National ID";
    case Passport = "Passport";
}

enum ReligiousAffiliation: string
{
    case Christianity = "Christianity";
    case Islam = "Islam";
    case Hinduism = "Hinduism";
    case Buddhism = "Buddhism";
    case Other = "Other";
}

enum AcademicLevel: string
{
    case PLE = "PLE";
    case OLevel = "O Level";
    case ALevel = "A Level";
    case Other = "Other";
}

enum DisciplineAction: string
{
    case Suspension = "Suspension";
    case Expulsion = "Expulsion";
}

enum CareerAspirations: string
{
    case Medicine = "Medicine";
    case Law = "Law";
    case Accountancy = "Accountancy";
    case Engineering = "Engineering";
    case InformationTechnology = "Information Technology";
    case Teaching = "Teaching";
    case Architecture = "Architecture";
    case BusinessManagement = "Business Management";
    case Journalism = "Journalism";
    case Psychology = "Psychology";
    case CreativeArts = "Creative Arts";
    case EnvironmentalScience = "Environmental Science";
    case Finance = "Finance";
    case Marketing = "Marketing";
    case Hospitality = "Hospitality";
    case Other = "Other";
}

enum Subjects: string
{
    case Mathematics = "Mathematics";
    case English = "English";
    case Biology = "Biology";
    case Chemistry = "Chemistry";
    case Physics = "Physics";
    case History = "History";
    case Geography = "Geography";
    case Literature = "Literature";
    case ComputerScience = "Computer Science";
    case Economics = "Economics";
    case BusinessStudies = "Business Studies";
    case Art = "Art";
    case CRE = "CRE";
    case GeneralPaper = "General Paper";
    case Divinity = "Divinity";
    case Other = "Other";
}

enum SubjectCode: string
{
    case Mathematics = "MAT";
    case English = "ENG";
    case Biology = "BIO";
    case Chemistry = "CHE";
    case Physics = "PHY";
    case History = "HIS";
    case Geography = "GEO";
    case Literature = "LIT";
    case ComputerScience = "CS";
    case Economics = "ECO";
    case BusinessStudies = "BS";
    case Art = "ART";
    case CRE = "CRE";
    case GeneralPaper = "GP";
    case Divinity = "DIV";
    case Other = "OTH";
}

enum SubjectCategory: string
{
    case Sciences = "Sciences";
    case Arts = "Arts";
    case Languages = "Languages";
    case Other = "Other";
}

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case Half_Day = 'half_day';
    case On_Leave = 'on_leave';
    case Holiday = 'holiday';
    case Weekend = 'weekend';
    case Remote = 'remote';
    case Field_Duty = 'field_duty';
    case Incomplete = 'incomplete';
}

enum CheckInMethod: string
{
    case Magic_Link = 'magic_link';
    case Manual = 'manual';
    case Admin_override = 'admin_override';
}

enum HolidayType: string
{
    case Public = 'Public Holiday';
    case School = 'School Holiday';
    case Religious = 'Religious Holiday';
    case National = 'National Holiday';
    case Other = 'Other Holiday';
}

enum RecurringPattern: string
{
    case Yearly = 'Yearly';
    case Easter_Based = 'Easter Based';
    case Hijri = 'Hijri';
    case Floating = 'Floating';
    case None = 'None';
}

enum LeaveType: string
{
    case Annual = "Annual";
    case Sick = "Sick";
    case Maternity = "Maternity";
    case Paternity = "Paternity";
    case Study = "Study";
    case Jury = "Jury";
    case Bereavement = "Bereavement";
}

enum LeaveStatus: string
{
    case Pending = "Pending";
    case Fully_Approved = "Fully Approved";
    case Rejected = "Rejected";
    case Partial = "Partial";
    case Awaiting_Replacement_Confirmation = "Awaiting Replacement Confirmation";
    case Replacement_Rejected = "Replacement Rejected";
}

enum ReplacementStatus: string
{
    case Pending = "Pending";
    case Accepted = "Accepted";
    case Rejected = "Rejected";
}

enum LeaveAction: string
{
    case Approved = "Approved";
    case Rejected = "Rejected";
    case Pending = "Pending";
}

enum OLevelClass: string
{
    case S1 = "S.1";
    case S2 = "S.2";
    case S3 = "S.3";
    case S4 = "S.4";
}
enum ALevelClass: string
{
    case S5 = "S.5";
    case S6 = "S.6";
}

enum Classes: string
{
    case S1 = "S.1";
    case S2 = "S.2";
    case S3 = "S.3";
    case S4 = "S.4";
    case S5 = "S.5";
    case S6 = "S.6";
}

// enum UceGrade: string
// {
//     case D1 = 'Distinction 1';
//     case D2 = 'Distinction 2';
//     case C3 = 'Credit 3';
//     case C4 = 'Credit 4';
//     case C5 = 'Credit 5';
//     case C6 = 'Credit 6';
//     case P7 = 'Pass 7';
//     case P8 = 'Pass 8';
//     case F9 = 'Fail 9';
// }

// enum UaceGrade: string
// {
//     case A = '6';
//     case B = '5';
//     case C = '4';
//     case D = '3';
//     case E = '2';
//     case O = '1';
//     case F = '0';
// }

enum GradingScaleName: string
{
    case UNEB_Traditional = 'UNEB Traditional';
    case Competency_Based = 'Competency-Based';
    case Internal_School = 'Internal School';
}

enum GradingScaleType: string
{
    case UNEB_Traditional = 'uneb_traditional';
    case UACE = 'uace';
    case Competency_Based = 'competency_based';
    case Custom = 'custom';
}

enum AchievementLevel: string
{
    case Excellent = 'Excellent';
    case Very_Good = 'Very Good';
    case Good = 'Good';
    case Above_Average = 'Above Average';
    case Average = 'Average';
    case Below_Average = 'Below Average';
    case Basic = 'Basic';
    case Pass = 'Pass';
    case Fail = 'Fail';
    case Standard = 'Standard';
    case Above_Standard = 'Above Standard';
    case Below_Standard = 'Below Standard';
    case Ungraded = 'Ungraded';
}

enum AssessmentTypeName: string
{
    case Coursework = 'Coursework';
    case Project = 'Project';
    case Practical = 'Practical';
    case AoI = 'Activity of Integration';
    case Test = 'Test';
}

enum AssessmentTypeCategory: string
{
    case Continuous = 'Continuous';
    case Exam = 'Exam';
    case AOI = 'AOI';
}

enum Term: string
{
    case Term1 = '1';
    case Term2 = '2';
    case Term3 = '3';
}

enum ExamType: string
{
    case Internal = 'internal';
    case External = 'external';

    public function label(): string
    {
        return match($this) {
            self::Internal => 'Internal Exam',
            self::External => 'External Exam',
        };
    }

    public static function options(): array
    {
        return [
            self::Internal->value => self::Internal->label(),
            self::External->value => self::External->label(),
        ];
    }
}

enum ExamStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case ResultsReleased = 'results_released';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'draft',
            self::Published => 'published',
            self::Ongoing => 'ongoing',
            self::Completed => 'completed',
            self::ResultsReleased => 'Results Released',
            default => ucfirst($this->value),
        };
    }
}

enum AoICriteria: string {
    case Relevance = 'Relevance';
    case Accuracy = 'Accuracy';
    case Coherence = 'Coherence';
    case Excellence = 'Excellence';
}

enum AoICriteriaCode: string {
    case Relevance = 'relevance';
    case Accuracy = 'accuracy';
    case Coherence = 'coherence';
    case Excellence = 'excellence';
}

enum ReportCardStatus: string
{
    case Draft = 'Draft';
    case Published = 'Published';
    case Printed = 'Printed';
}
enum AdmissionStatus: string
{
    case Pending = 'pending';
    case Admitted = 'admitted';
    case Rejected = 'rejected';
}

enum CompetencyBasedScale: string
{
    case A = 'Achieved Excellence';
    case B = 'Achieved Above Standard';
    case C = 'Achieved Standard';
    case D = 'Achieved Basic Competency';
    case E = 'Below Standard';
    case U = 'Ungraded';
}
