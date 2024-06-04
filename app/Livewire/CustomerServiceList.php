<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;

class CustomerServiceList extends Component
{
    // public $services;
    public $selectedCategory; 
    public $sort;
    public $search;

    use WithPagination;

    public function mount()
    {
        $this->filterServices();
    }

   public function filterServices()
    {
            // $query = Service::where('Availability_status', '!=', 'Not Available')
            // ->whereNull('deleted_at');

            // Retrieve all rows except for those with "Tarpaulin" in their service_name
        $query1 = Service::with('category')
        ->where('Availability_status', '!=', 'Not Available')
        ->where('service_name', 'not like', '%Tarpaulin%')
        ->whereNull('deleted_at');

        // Retrieve only one row with "Tarpaulin" in its service_name
        $query2 = Service::with('category')
            ->where('Availability_status', '!=', 'Not Available')
            ->where('service_name', 'like', '%Tarpaulin%')
            ->whereNull('deleted_at')
            ->take(1);

        // Combine both queries
        $query = $query1->union($query2);

            if ($this->selectedCategory !== 'All') {
                $category = ServiceCategory::where('category_name', $this->selectedCategory)->first();
                
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
        
            // Sort the services based on the sort parameter
            if ($this->sort === 'az') {
                $query->orderBy('service_name', 'asc');
            } elseif ($this->sort === 'za') {
                $query->orderBy('service_name', 'desc');
            }
            elseif ($this->sort === 'lh') {
                $query->orderBy('price', 'asc');
            }
            elseif ($this->sort === 'hl') {
                $query->orderBy('price', 'desc');
            }
            elseif ($this->sort === 'on') {
                $query->orderBy('created_at', 'asc');
            }
            elseif ($this->sort === 'no') {
                $query->orderBy('created_at', 'desc');
            }
        
            // $this->services = $query->get();
            $this->services = $query->get();
           
        }


    public function render(): View
    {
        // Retrieve all rows except for those with "Tarpaulin" in their service_name
        $query1 = Service::with('category')
            ->where('Availability_status', '!=', 'Not Available')
            ->where('service_name', 'not like', '%Tarpaulin%')
            ->whereNull('deleted_at');

        // Retrieve only one row with "Tarpaulin" in its service_name
        $query2 = Service::with('category')
            ->where('Availability_status', '!=', 'Not Available')
            ->where('service_name', 'like', '%Tarpaulin%')
            ->whereNull('deleted_at')
            ->take(1);

        // Combine both queries
        $query = $query1->union($query2);

        if ($this->selectedCategory !== 'All') {
            $category = ServiceCategory::where('category_name', $this->selectedCategory)->first();
            
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
    
        if ($this->sort === 'az') {
            $query->orderBy('service_name', 'asc');
        } elseif ($this->sort === 'za') {
            $query->orderBy('service_name', 'desc');
        }
        elseif ($this->sort === 'lh') {
            $query->orderBy('price', 'asc');
        }
        elseif ($this->sort === 'hl') {
            $query->orderBy('price', 'desc');
        }
        elseif ($this->sort === 'on') {
            $query->orderBy('created_at', 'asc');
        }
        elseif ($this->sort === 'no') {
            $query->orderBy('created_at', 'desc');
        }

         if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->where('service_name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
    
        // $this->services = $query->get();
        $services = $query->paginate(30);

        return view('livewire.customer-service-list',  [
            'categories' => ServiceCategory::all(),
            'services' => $services,
        ]);
    }
}