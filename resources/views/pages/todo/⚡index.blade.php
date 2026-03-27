<?php

use Livewire\Component;
use App\Models\Todo as Task;
use Livewire\Attributes\Rule;

new class extends Component
{
    #[Rule('required|min:3', message: 'Task name is too short!')]
    public $task_name = '';
    public $theme = 'dark';

    public function getTasks()
    {
        sleep(5);
        return Task::latest()->orderBy('is_completed', 'asc')->get();
    }

    public function createTask()
    {
        $this->validate();

        Task::create([
            'task_name' => $this->task_name,
            'is_completed' => false,
        ]);

        $this->task_name = '';
        
        // Use browser events for notifications if desired
        $this->dispatch('task-added');
    }

    public function toggleTask($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->is_completed = !$task->is_completed;
            $task->save();
        }
    }

    public function deleteTask($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->delete();
        }
    }

    public function toggleTheme(){
        $this->theme = $this->theme === 'dark' ? 'light' : 'dark';
    }
};
?>

<div class="min-h-screen transition-colors duration-500 font-sans antialiased selection:bg-indigo-500/30 {{ $theme === 'dark' ? 'bg-[#0f172a] text-slate-200' : 'bg-slate-50 text-slate-900' }}"
     x-data="{ showForm: false }"
     @task-added.window="showForm = false">
    
    <!-- Background Accents -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full opacity-50 blur-[120px] {{ $theme === 'dark' ? 'bg-indigo-500/10' : 'bg-indigo-500/5' }}"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] rounded-full opacity-50 blur-[120px] {{ $theme === 'dark' ? 'bg-indigo-500/10' : 'bg-indigo-500/5' }}"></div>
    </div>

    <div class="relative max-w-2xl mx-auto px-6 py-12 lg:py-24">
        <!-- Header Section -->
        <header class="mb-12 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight lg:text-4xl {{ $theme === 'dark' ? 'text-white' : 'text-slate-900' }}">
                        Todo Live
                    </h1>
                    <p class="mt-2 text-slate-400 font-medium">
                        Stay organized, breathe and focus.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        @click="showForm = !showForm"
                        class="group relative inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 font-medium text-white transition-all hover:bg-indigo-500 hover:shadow-[0_0_20px_rgba(79,70,229,0.4)] focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ $theme === 'dark' ? 'focus:ring-offset-slate-900' : 'focus:ring-offset-white' }}"
                    >
                        <svg x-show="!showForm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 transition-transform group-hover:rotate-90">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <svg x-show="showForm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <button 
                        wire:click="toggleTheme" 
                        class="group relative inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all border font-medium {{ $theme === 'dark' ? 'bg-slate-800 border-slate-700 text-yellow-400 hover:bg-slate-700' : 'bg-white border-slate-200 text-indigo-600 hover:bg-slate-50 hover:border-slate-300 shadow-sm' }}"
                    >
                        @if($theme === 'dark')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.743-4.386l-1.591 1.591M3 12h2.25m.386-4.743l1.591-1.591M12 18.75V21m-4.743-4.386l-1.591 1.591M12 7.5a4.5 4.5 0 110 9 4.5 4.5 0 010-9z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        @endif
                    </button>
                </div>
            </div>
        </header>

        <!-- New Task Form -->
        <div x-show="showForm" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="mb-8"
        >
            <form wire:submit.prevent="createTask" class="relative group">
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="task_name"
                    placeholder="What needs to be done?"
                    class="w-full border-2 rounded-2xl py-4 pl-6 pr-16 backdrop-blur-sm transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/10 {{ $theme === 'dark' ? 'bg-slate-800/50 border-slate-700/50 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-white border-slate-200 text-slate-900 placeholder-slate-400 focus:border-indigo-500 shadow-sm' }} @error('task_name') border-red-500/50 @enderror"
                    autofocus
                >
                <div class="absolute right-3 top-2 bottom-2">
                    <button type="submit" class="h-full px-6 rounded-xl font-semibold transition-colors disabled:opacity-50 {{ $theme === 'dark' ? 'bg-slate-700 hover:bg-slate-600 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white' }}" wire:loading.attr="disabled">
                        <span wire:loading.remove>Add Task</span>
                        <span wire:loading>Adding...</span>
                    </button>
                </div>
                @error('task_name')
                    <span class="absolute -bottom-6 left-2 text-xs font-bold text-red-400 uppercase tracking-wider">{{ $message }}</span>
                @enderror
            </form>
        </div>

        <!-- Task List Items -->
        <main class="space-y-4">
            @forelse ($this->getTasks() as $task)
                <div 
                    wire:key="task-{{ $task->id }}"
                    class="group relative flex items-center justify-between p-4 border rounded-2xl backdrop-blur-md transition-all {{ $theme === 'dark' ? 'bg-slate-800/40 border-slate-700/50 hover:bg-slate-800/60 hover:border-slate-600/50' : 'bg-white border-slate-200 hover:border-slate-300 shadow-sm' }}"
                >
                    <div class="flex items-center gap-4 flex-1">
                        <!-- Custom Checkbox -->
                        <button 
                            wire:click="toggleTask({{ $task->id }})"
                            class="flex-shrink-0 w-6 h-6 rounded-lg border-2 transition-all flex items-center justify-center {{ $task->is_completed ? 'bg-emerald-500 border-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : ($theme === 'dark' ? 'border-slate-600 group-hover:border-indigo-500' : 'border-slate-300 group-hover:border-indigo-500') }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4 text-white {{ $task->is_completed ? 'scale-100 opacity-100' : 'scale-50 opacity-0' }} transition-all duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>

                        <span class="text-sm sm:text-lg font-medium transition-all duration-300 {{ $task->is_completed ? ($theme === 'dark' ? 'text-slate-500 line-through' : 'text-slate-400 line-through') : ($theme === 'dark' ? 'text-slate-100' : 'text-slate-800') }}">
                            {{ $task->task_name }}
                        </span>
                    </div>

                    {{-- Delete Button --}}
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button 
                            wire:click="deleteTask({{ $task->id }})"
                            wire:confirm="Are you sure you want to delete this task?"
                            class="p-2 transition-colors {{ $theme === 'dark' ? 'text-slate-400 hover:text-red-400' : 'text-slate-400 hover:text-red-500' }}"
                            title="Delete Task"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.123a2.25 2.25 0 00-2.25-2.25h-4.5a2.25 2.25 0 00-2.25 2.25v1.123m9.966 0c.33.019.66.04 1.022.166" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center space-y-4">
                    <div class="p-6 rounded-full {{ $theme === 'dark' ? 'bg-slate-800/30' : 'bg-slate-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375m1.875-3h1.875m-1.875 6h1.875M9 21h3v-3H9v3zm3.75 0h3v-3h-3v3zm3.75 0h3v-3h-3v3zM9 9h3V6H9v3zm3.75 0h3V6h-3v3zm3.75 0h3V6h-3v3z" />
                        </svg>

                    </div>
                    <div>
                        <h3 class="text-xl font-bold {{ $theme === 'dark' ? 'text-slate-400' : 'text-slate-600' }}">No tasks yet</h3>
                        <p class="text-slate-500">Tap the plus button to add your first task!</p>
                    </div>
                </div>
            @endforelse
        </main>

        <!-- Footer Info -->
        <footer class="mt-16 text-center">
            <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">
                Built with Livewire & Tailwind CSS
            </p>
        </footer>
    </div>
</div>