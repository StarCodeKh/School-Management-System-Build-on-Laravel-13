<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    /** add teacher page */
    public function teacherAdd()
    {
        // only teacher accounts that don't have a teacher profile yet
        $users = User::where('role_name', Role::Teacher)->doesntHave('teacher')->orderBy('name')->get();
        return view('teacher.add-teacher',compact('users'));
    }

    /** teacher list */
    public function teacherList()
    {
        $listTeacher = Teacher::with('user')->latest()->get();
        return view('teacher.list-teachers',compact('listTeacher'));
    }

    /** teacher Grid */
    public function teacherGrid()
    {
        $teacherGrid = Teacher::all();
        return view('teacher.teachers-grid',compact('teacherGrid'));
    }

    /** Save Record */
    public function saveRecord(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id|unique:teachers,user_id',
        ] + $this->profileRules());

        try {
            Teacher::create($validated + [
                'full_name' => User::findOrFail($validated['user_id'])->name,
            ]);

            return redirect()->back()->with('success', 'Teacher record saved successfully!');
        } catch(\Exception $e) {
            Log::error('Failed to save Teacher record', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save Teacher record. Please try again.');
        }
    }

    /** Edit Record */
    public function editRecord(Teacher $teacher)
    {
        $teacher->load('user');
        return view('teacher.edit-teacher',compact('teacher'));
    }

    /** Update Record */
    public function updateRecordTeacher(Request $request)
    {
        $validated = $request->validate([
            'id'        => 'required|exists:teachers,id',
            'full_name' => 'required|string|max:255',
        ] + $this->profileRules());

        try {
            Teacher::findOrFail($validated['id'])->update($validated);
            return redirect()->back()->with('success', 'Teacher record updated successfully!');
        } catch(\Exception $e) {
            Log::error('Failed to update Teacher record', ['error' => $e->getMessage(), 'teacher_id' => $validated['id']]);
            return redirect()->back()->with('error', 'Failed to update Teacher record. Please try again.');
        }
    }

    /** Delete Record */
    public function teacherDelete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:teachers,id',
        ]);

        try {
            Teacher::findOrFail($validated['id'])->delete();
            return redirect()->back()->with('success', 'Teacher record deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete Teacher record', ['error' => $e->getMessage(), 'teacher_id' => $validated['id']]);
            return redirect()->back()->with('error', 'Failed to delete Teacher record. Please try again.');
        }
    }

    /** fields shared by the add and edit forms */
    private function profileRules(): array
    {
        return [
            'gender'        => 'required|string|max:20',
            'experience'    => 'required|integer|min:0|max:80',
            'date_of_birth' => 'required|date',
            'qualification' => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'zip_code'      => 'required|string|max:20',
            'country'       => 'required|string|max:100',
        ];
    }
}
