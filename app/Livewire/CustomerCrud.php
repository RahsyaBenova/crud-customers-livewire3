<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class CustomerCrud extends Component
{
    use WithPagination;

    public $inputs;
    public $isModalOpen = 0;
    public $isEditMode = false;
    public $customerId = null;
    public $name, $email, $phone, $description;
    public $searchTerm = '';
    public $perPage = 5;

    public function mount()
    {
        $this->inputs = collect([['name' => '', 'email' => '', 'phone' => '', 'description' => '']]);
    }

    public function render()
    {
        $customers = Customer::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('email', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('phone', 'like', '%' . $this->searchTerm . '%')
            ->paginate($this->perPage);

        return view('livewire.customer-crud', compact('customers'));
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }
    public function addInput()
    {
        $this->inputs->push(['name' => '', 'email' => '', 'phone' => '', '' => 'description']);
    }
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->inputs = collect([['name' => '', 'email' => '', 'phone' => '', 'description' => '']]);
        $this->customerId = null;
        $this->isEditMode = false;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->description = '';
    }

    public function removeInput($key)
    {
        $this->inputs->pull($key);
    }

    public function store()
    {
        if ($this->isEditMode) {
            $this->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|min:10|numeric',
                'description' => 'nullable|string',
            ]);

            $customer = Customer::find($this->customerId);
            $customer->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'description' => $this->description,
            ]);

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Data updated successfully!',
                'icon' => 'success',
            ]);
        } else {
            $this->validate([
                'inputs.*.name' => 'required',
                'inputs.*.email' => 'required|email',
                'inputs.*.phone' => 'required|min:10|numeric',
                'inputs.*.description' => 'nullable|string',
            ]);

            foreach ($this->inputs as $input) {
                Customer::create($input);
            }

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Data saved successfully!',
                'icon' => 'success',
            ]);
        }

        $this->closeModal();
    }

    public function edit($id)
{
    $customer = Customer::findOrFail($id);

    $this->customerId = $id;
    $this->name = $customer->name;
    $this->email = $customer->email;
    $this->phone = $customer->phone;
    $this->description = $customer->description;

    $this->isEditMode = true;

    $this->openModal();
}
public function delete($id)
{
    Customer::find($id)->delete();

    $this->dispatch('swal',[
        'title'=>'Success!',
        'text'=>'Data delete succesfully!',
        'icon'=>'success',
      ]);
}
public function edit2($id)
{
    return redirect()->to("/customers/edit/{$id}");
}

}
