<?php

namespace App\Enum;

enum UserRole: string {
    case Admin =  'Admin';
    case Donor =  'Donor';
    case Patient = 'Patient';
    case Association = 'Association';
}