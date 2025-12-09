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
