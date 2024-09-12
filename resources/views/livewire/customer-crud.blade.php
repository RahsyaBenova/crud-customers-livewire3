<div class="container" x-data="{ description: @entangle('description') }">
    <!-- Notifications -->
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search..." wire:model.live="searchTerm">
    </div>

    <!-- Rows per Page -->
    <div class="mb-3">
        <label>Show</label>
        <select wire:model="perPage" class="form-control d-inline-block" style="width: auto;">
            <option value="5">5</option>
            <option value="8">8</option>
            <option value="10">10</option>
        </select>
        <label>entries</label>
    </div>

    <!-- Button to Open Modal for Create -->
    <button class="btn btn-primary" wire:click="openModal()">Create Customers</button>

    <!-- Data Table -->
    <table class="table table-bordered mt-5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{!! $customer->description !!}</td>
                <td>
                    <button class="btn btn-info" wire:click="edit({{ $customer->id }})">Edit</button>
                    <button class="btn btn-danger" wire:click="delete({{ $customer->id }})">Delete</button>
                    {{-- <button class="btn btn-info" wire:click="edit2({{ $customer->id }})">Edit</button> --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    {{ $customers->links() }}

    <!-- Modal -->
    @if($isModalOpen)
    <div class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? 'Edit Customer' : 'Create Multiple Customers' }}
                    </h5>
                    <button type="button" class="close" wire:click="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    @if($isEditMode)
                    <!-- Edit Mode: Single Customer -->
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Enter Name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" wire:model="email" class="form-control" placeholder="Enter Email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" wire:model="phone" class="form-control" placeholder="Enter Phone">
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" wire:ignore x-data x-init="
    ClassicEditor
    .create($refs.editor)
    .then(editor => {
        editor.model.document.on('change:data', () => {
            @this.set('description', editor.getData());
        });
    })
    .catch(error => {
        console.error(error);
    });
">
    <label>Description</label>
    <textarea x-ref="editor" wire:model.defer="description" class="form-control" rows="4">{{ $description }}</textarea>
    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
</div>

                    @else
                    <!-- Create Multiple Customers -->
                    @foreach($inputs as $key => $input)
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" wire:model="inputs.{{ $key }}.name" class="form-control" placeholder="Enter Name">
                            @error('inputs.' . $key . '.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <input type="email" wire:model="inputs.{{ $key }}.email" class="form-control" placeholder="Enter Email">
                            @error('inputs.' . $key . '.email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <input type="text" wire:model="inputs.{{ $key }}.phone" class="form-control" placeholder="Enter Phone">
                            @error('inputs.' . $key . '.phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12" wire:ignore x-data x-init="
                            ClassicEditor
                            .create($refs['editor{{ $key }}'])
                            .then(editor => {
                                editor.model.document.on('change:data', () => {
                                    @this.set('inputs.{{ $key }}.description', editor.getData());
                                });
                            })
                            .catch(error => {
                                console.error(error);
                            });
                        ">
                            <label>Description</label>
                            <textarea x-ref="editor{{ $key }}" wire:model.defer="inputs.{{ $key }}.description" class="form-control" rows="4"></textarea>
                            @error('inputs.' . $key . '.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        @if(!$isEditMode)
                        <div class="col-md-1">
                            <button class="btn btn-danger btn-sm" wire:click="removeInput({{ $key }})">Remove</button>
                        </div>
                        @endif
                    </div>
                    @endforeach
                    @endif
                </div>
    
                <div class="modal-footer">
                    @if(!$isEditMode)
                    <button class="btn btn-secondary" wire:click="addInput()">Add Row</button>
                    @endif
                    <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="store()">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    
<script>
    document.addEventListener('trix-change', function(e) {
        @this.set('description', e.target.value);
    });
</script>
    <script>
        document.addEventListener('trix-change', function(e) {
            @this.set('description', e.target.value);
        });
    </script>
    <script>
        document.addEventListener('trix-change', function(e) {
            var key = e.target.getAttribute('input').replace('x', '');
            @this.set(`inputs.${key}.description`, e.target.value);
        });
    </script>
    <script>
        document.addEventListener('livewire:initialized',()=>{
    
            @this.on('swal',(event)=>{
                const data=event
                swal.fire({
                    icon:data[0]['icon'],
                    title:data[0]['title'],
                    text:data[0]['text'],
                })
            })
    
            @this.on('delete-prompt',(event)=>{
                swal.fire({
                    title:'Are you sure?',
                    text:'You are about to delete this record, this action is irreversible',
                    icon:'warning',
                    showCancelButton:true,
                    confirmButtonColor:'#3085d6',
                    showCancelButtonColor:'#d33',
                    confirmButtonText:'Yes, Delete it!',
                }).then((result)=>{
                    if(result.isConfirmed){
                        @this.dispatch('goOn-Delete')
    
                        @this.on('deleted',(event)=>{
                           swal.fire({
                            title:'Deleted',
                            text:'Your record has been deleted',
                            icon:'success',
                           })
                        })
                    }
                })
            })
    
    
        })
    </script>
    
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('productAdded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Added',
                    text: 'The product has been successfully added!',
                });
            });
    
            Livewire.on('productUpdated', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Updated',
                    text: 'The product has been successfully updated!',
                });
            });
    
            Livewire.on('productDeleted', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Deleted',
                    text: 'The product has been successfully deleted!',
                });
            });
        });
    </script>
</div>
