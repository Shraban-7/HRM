<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\CompanyPolicy;
use App\Models\CompanySetting;
use App\Models\Department;
use App\Models\DocumentCategory;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Holiday;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\OnboardingChecklist;
use App\Models\OnboardingTask;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company Settings & White-labeling
        CompanySetting::set('company_name', 'Nexus Technologies Inc.', 'general');
        CompanySetting::set('company_email', 'contact@nexustechnologies.io', 'general');
        CompanySetting::set('company_phone', '+1 (800) 555-0199', 'general');
        CompanySetting::set('company_address', '500 Innovation Parkway, Suite 1000, Austin, TX 78701', 'general');
        CompanySetting::set('company_website', 'https://nexustechnologies.io', 'general');
        CompanySetting::set('currency_code', 'USD', 'general');
        CompanySetting::set('currency_symbol', '$', 'general');
        CompanySetting::set('timezone', 'America/Chicago', 'general');
        CompanySetting::set('date_format', 'Y-m-d', 'general');

        // 2. Public Holidays
        Holiday::firstOrCreate(['name' => "New Year's Day", 'date' => date('Y').'-01-01'], ['is_recurring' => true]);
        Holiday::firstOrCreate(['name' => 'Independence Day', 'date' => date('Y').'-07-04'], ['is_recurring' => true]);
        Holiday::firstOrCreate(['name' => 'Thanksgiving Day', 'date' => date('Y').'-11-26'], ['is_recurring' => true]);
        Holiday::firstOrCreate(['name' => 'Christmas Day', 'date' => date('Y').'-12-25'], ['is_recurring' => true]);

        // 3. Asset Categories & Hardware Assets
        $lapCat = AssetCategory::firstOrCreate(['code' => 'LAP'], ['name' => 'Laptops & Workstations']);
        $monCat = AssetCategory::firstOrCreate(['code' => 'MON'], ['name' => 'External Displays']);
        $phnCat = AssetCategory::firstOrCreate(['code' => 'PHN'], ['name' => 'Mobile Test Devices']);

        Asset::firstOrCreate(['asset_tag' => 'LAP-2026-001'], [
            'asset_category_id' => $lapCat->id,
            'name' => 'Apple MacBook Pro 16" M3 Max',
            'serial_number' => 'C02XYZ123456',
            'model' => '36GB RAM / 1TB SSD',
            'cost' => 3499.00,
            'purchase_date' => date('Y-m-d', strtotime('-6 months')),
            'status' => 'available',
        ]);

        Asset::firstOrCreate(['asset_tag' => 'LAP-2026-002'], [
            'asset_category_id' => $lapCat->id,
            'name' => 'Dell XPS 15 9530',
            'serial_number' => 'DELL-987654321',
            'model' => 'i9 / 32GB RAM / RTX 4070',
            'cost' => 2299.00,
            'purchase_date' => date('Y-m-d', strtotime('-3 months')),
            'status' => 'available',
        ]);

        Asset::firstOrCreate(['asset_tag' => 'MON-2026-010'], [
            'asset_category_id' => $monCat->id,
            'name' => 'Dell UltraSharp 32" 4K USB-C Monitor',
            'serial_number' => 'MON-DELL-4K-01',
            'model' => 'U3223QE',
            'cost' => 849.00,
            'purchase_date' => date('Y-m-d', strtotime('-4 months')),
            'status' => 'available',
        ]);

        // 4. Document Categories & Corporate Policies
        DocumentCategory::firstOrCreate(['name' => 'Identification & Passports']);
        DocumentCategory::firstOrCreate(['name' => 'Employment Contracts']);
        DocumentCategory::firstOrCreate(['name' => 'Tax Forms & Declarations']);
        DocumentCategory::firstOrCreate(['name' => 'Academic & Certifications']);

        CompanyPolicy::firstOrCreate(['title' => 'Information Security & Data Protection Policy'], [
            'description' => 'Mandatory enterprise procedures governing credential security, encryption, and customer data handling.',
            'published_at' => date('Y-m-d'),
            'requires_acknowledgement' => true,
        ]);

        CompanyPolicy::firstOrCreate(['title' => 'Remote Work & Telecommuting Guidelines'], [
            'description' => 'Standard core hours, reimbursement for home office equipment, and communication expectations.',
            'published_at' => date('Y-m-d'),
            'requires_acknowledgement' => true,
        ]);

        // 5. Expense Categories & Sample Claims
        $travel = ExpenseCategory::firstOrCreate(['code' => 'TRV'], ['name' => 'Business Travel & Flights', 'max_limit' => 2500]);
        $meals = ExpenseCategory::firstOrCreate(['code' => 'MEAL'], ['name' => 'Client Entertainment & Meals', 'max_limit' => 500]);
        $supplies = ExpenseCategory::firstOrCreate(['code' => 'OFF'], ['name' => 'Office & Software Subscriptions', 'max_limit' => 1000]);

        $firstEmployee = Employee::first();
        $adminUser = User::where('email', 'admin@hrm.com')->first();

        if ($firstEmployee) {
            Expense::firstOrCreate([
                'employee_id' => $firstEmployee->id,
                'claim_date' => date('Y-m-d', strtotime('-2 days')),
                'amount' => 185.50,
            ], [
                'expense_category_id' => $meals->id,
                'description' => 'Working lunch with European enterprise client executives.',
                'status' => 'approved',
                'approved_by' => $adminUser?->id,
            ]);

            // 6. Sample Employee Loan
            $loan = Loan::firstOrCreate([
                'employee_id' => $firstEmployee->id,
                'loan_amount' => 3000.00,
            ], [
                'interest_rate' => 3.5,
                'term_months' => 12,
                'monthly_installment' => 258.75,
                'total_repayable' => 3105.00,
                'remaining_balance' => 2846.25,
                'status' => 'active',
                'approved_by' => $adminUser?->id,
                'disbursed_at' => now()->subMonth(),
            ]);

            LoanRepayment::firstOrCreate([
                'loan_id' => $loan->id,
                'repayment_date' => date('Y-m-d', strtotime('-5 days')),
            ], [
                'amount_paid' => 258.75,
                'payment_method' => 'payroll_deduction',
            ]);

            // 7. Sample Support Tickets
            $ticket = Ticket::firstOrCreate([
                'ticket_number' => 'TICK-PAY001',
            ], [
                'employee_id' => $firstEmployee->id,
                'department' => 'Payroll',
                'subject' => 'Tax deduction itemization inquiry on latest payslip',
                'description' => 'Could payroll support clarify the state withholding tax percentage applied to overtime pay?',
                'priority' => 'medium',
                'status' => 'in_progress',
                'assigned_to' => $adminUser?->id,
            ]);

            TicketComment::firstOrCreate([
                'ticket_id' => $ticket->id,
                'user_id' => $adminUser?->id ?? 1,
                'comment' => 'Reviewing the supplemental tax bracket schedule. A revised statement will be uploaded shortly.',
            ], [
                'is_internal' => false,
            ]);
        }

        // 8. Onboarding Checklists & Tasks
        $engDept = Department::where('name', 'like', '%Eng%')->first();
        $checklist = OnboardingChecklist::firstOrCreate(['title' => 'Core Enterprise Onboarding Checklist'], [
            'department_id' => $engDept?->id,
            'description' => 'Mandatory orientation, compliance training, and hardware allocation for new joiners.',
        ]);

        OnboardingTask::firstOrCreate(['checklist_id' => $checklist->id, 'title' => 'Submit Government ID & Tax Form W-4 / W-8'], [
            'description' => 'Upload scanned copies to Digital Documents folder.',
            'assigned_role' => 'employee',
            'is_required' => true,
        ]);

        OnboardingTask::firstOrCreate(['checklist_id' => $checklist->id, 'title' => 'IT Hardware Allocation & Security Key Handover'], [
            'description' => 'Receive laptop asset and configure 2FA hardware key.',
            'assigned_role' => 'it_support',
            'is_required' => true,
        ]);

        OnboardingTask::firstOrCreate(['checklist_id' => $checklist->id, 'title' => 'Sign Information Security & Employee Handbook'], [
            'description' => 'Digital acknowledgement on policies portal.',
            'assigned_role' => 'employee',
            'is_required' => true,
        ]);
    }
}
