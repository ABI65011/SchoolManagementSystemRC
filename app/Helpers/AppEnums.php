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
