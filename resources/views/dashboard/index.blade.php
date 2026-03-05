@extends('layouts.app')

@section('content')
<style>
  [x-cloak] { display: none !important; }
</style>

<div x-data="weekCalendar()" x-init="init()" x-cloak class="bg-white rounded shadow p-4 mx-2 relative">
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-xl font-bold text-blue-800">AGENDA ÁREA COMERCIAL Y DISEÑO</h2>
    <div class="flex items-center space-x-4">
      <span class="text-xs text-blue-800 font-semibold">Bienvenido, {{ Auth::user()->nombre }}</span>
      <button class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded shadow-sm"
              onclick="document.getElementById('logout-form').submit();">
        Cerrar Sesión
      </button>
      <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
      </form>
    </div>
  </div>
  
  <div class="flex items-center justify-between mb-4" x-cloak>
    <div>
      <strong>Semana del:</strong>
      <span x-text="formatDate(startDate)"></span> -
      <span x-text="formatDate(endDate())"></span>
      <button @click="previousWeek()"
              class="bg-blue-200 text-blue-900 font-semibold text-center py-1 px-2 rounded shadow-sm text-xs ml-2">
        Anterior
      </button>
      <button @click="nextWeek()"
              class="bg-blue-200 text-blue-900 font-semibold text-center py-1 px-2 rounded shadow-sm text-xs ml-2">
        Siguiente
      </button>
    </div>
    <div class="flex space-x-2">
      <button @click="currentFilter = 'todos'" 
              :class="{'bg-gray-300': currentFilter==='todos', 'bg-green-200': currentFilter!=='todos'}"
              class="text-green-900 font-semibold text-center py-1 px-2 rounded shadow-sm text-xs">
        Todos
      </button>
      <button @click="currentFilter = 'comercial'" 
              :class="{'bg-green-300': currentFilter==='comercial', 'bg-green-200': currentFilter!=='comercial'}"
              class="text-green-900 font-semibold text-center py-1 px-2 rounded shadow-sm text-xs">
        Comercial
      </button>
      <button @click="currentFilter = 'diseño'" 
              :class="{'bg-purple-300': currentFilter==='diseño', 'bg-purple-200': currentFilter!=='diseño'}"
              class="text-purple-900 font-semibold text-center py-1 px-2 rounded shadow-sm text-xs">
        Diseño
      </button>
    </div>
  </div>
  
  <!-- Tabla de agenda -->
  <div class="overflow-x-auto" x-cloak>
    <table class="w-full border-collapse">
      <thead class="divide-x divide-gray-200">
        <tr class="bg-blue-200 text-blue-900 text-xs font-semibold">
          <th class="p-2 text-left w-24">Usuarios</th>
          <template x-for="(day, index) in weekDays" :key="index">
            <th class="p-2 text-center" x-text="formatDisplayDate(day)"></th>
          </template>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @php
          $users = [
            ['name' => 'Rodrigo Esparza', 'type' => 'comercial'],
            ['name' => 'Paola Yanquez', 'type' => 'comercial'],
            ['name' => 'Francisca Perez', 'type' => 'comercial'],
            ['name' => 'Loreto Contreras', 'type' => 'comercial'],
            ['name' => 'Rodrigo Calderon', 'type' => 'diseño'],
            ['name' => 'Rodrigo Gonzalez', 'type' => 'diseño'],
            ['name' => 'Constanza Diaz', 'type' => 'diseño'],

          ];
        @endphp

        @foreach($users as $user)
          <tr class="divide-x divide-gray-200" x-show="currentFilter==='todos' || currentFilter==='{{ $user['type'] }}'">
            <td class="p-2 text-xs text-blue-800 font-bold text-right w-24 whitespace-nowrap">
              {{ $user['name'] }}
            </td>
            @for($i = 0; $i < 7; $i++)
              <td class="p-2">
                <div @click="handleCellClick('{{ $user['name'] }}', weekDays[{{ $i }}])"
                     :class="{'bg-white': getEvent('{{ $user['name'] }}', weekDays[{{ $i }}]), 'bg-gray-50': !getEvent('{{ $user['name'] }}', weekDays[{{ $i }}])}"
                     class="w-24 h-24 mx-auto border border-gray-200 flex items-center justify-center rounded cursor-pointer hover:bg-blue-100 transition-colors p-1">
                  <template x-if="getEvent('{{ $user['name'] }}', weekDays[{{ $i }}])">
                    <span class="text-xs text-black" x-text="truncate(getEvent('{{ $user['name'] }}', weekDays[{{ $i }}]).descripcion)"></span>
                  </template>
                  <template x-if="!getEvent('{{ $user['name'] }}', weekDays[{{ $i }}])">
                    <span class="text-xs text-gray-500">+</span>
                  </template>
                </div>
              </td>
            @endfor
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  
  <div x-show="modalOpen" 
       x-cloak 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-90"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-90"
       class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded shadow-lg p-6 w-11/12 max-w-md">
      <h3 class="text-lg font-bold mb-4" x-text="isEditing ? 'Editar Evento' : 'Agregar Evento'"></h3>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Fecha (individual)</label>
        <input type="text" x-model="selectedDate" class="mt-1 block w-full border border-gray-300 rounded-md p-2" readonly>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Usuario</label>
        <input type="text" x-model="selectedUser" class="mt-1 block w-full border border-gray-300 rounded-md p-2" readonly>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Seleccionar fechas adicionales</label>
        <div class="mt-1">
          <template x-for="(day, index) in weekDays" :key="index">
            <label class="inline-flex items-center mr-2">
              <input type="checkbox" :value="day" x-model="selectedDates" class="form-checkbox h-4 w-4 text-blue-600">
              <span class="ml-1 text-xs text-gray-700" x-text="formatDisplayDate(day)"></span>
            </label>
          </template>
        </div>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea x-model="description" class="mt-1 block w-full border border-gray-300 rounded-md p-2" rows="3" placeholder="Ingrese la descripción del evento"></textarea>
      </div>
      <div class="flex justify-end space-x-2">
        <button @click="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
          Cancelar
        </button>
        <button x-show="!isEditing" @click="saveEvent()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
          Guardar
        </button>
        <button x-show="isEditing" @click="updateEvent()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
          Actualizar
        </button>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function weekCalendar() {
  return {
    /* ---------------- estado ---------------- */
    sessionUser: "{{ Auth::user()->nombre }}",
    startDate: getMonday(new Date()),
    modalOpen: false,
    selectedUser: '',
    selectedDate: '',
    selectedDates: [],
    description: '',
    currentFilter: 'todos',
    events: [],
    isEditing: false,
    editingEventId: null,

    init() { this.fetchEvents(); },

    /* ---------------- peticiones ---------------- */
    fetchEvents() {
      fetch("{{ route('eventos.all') }}")
        .then(r => r.json())
        .then(data => (this.events = data))
        .catch(console.error);
    },

    /* ---------------- helpers de fechas ---------------- */
    get weekDays() {
      let days = [];
      for (let i = 0; i < 7; i++) {
        let d = new Date(this.startDate);
        d.setDate(d.getDate() + i);
        days.push(this.formatDateShort(d));
      }
      return days;
    },
    formatDateShort(d) {
      // Ahora incluye el año completo para evitar confusión entre años
      return d.getDate().toString().padStart(2,'0') + '/' +
             (d.getMonth()+1).toString().padStart(2,'0') + '/' +
             d.getFullYear();
    },
    formatDisplayDate(dateStr) {
      // Muestra solo día/mes en la interfaz pero internamente usa día/mes/año
      let parts = dateStr.split('/');
      return parts[0] + '/' + parts[1];
    },
    formatDate(d) {
      return d.getDate().toString().padStart(2,'0') + '/' +
             (d.getMonth()+1).toString().padStart(2,'0') + '/' +
             d.getFullYear();
    },
    endDate()  { let d=new Date(this.startDate); d.setDate(d.getDate()+6); return d; },
    previousWeek(){ this.startDate.setDate(this.startDate.getDate()-7); this.startDate=new Date(this.startDate); },
    nextWeek(){ this.startDate.setDate(this.startDate.getDate()+7); this.startDate=new Date(this.startDate); },

    /* ---------------- UI: modal ---------------- */
    openModal(user,dayText){
      if(user!==this.sessionUser){
        Swal.fire('Acceso denegado','Solo puedes crear eventos en tu propia fila','warning');return;
      }
      this.isEditing=false;
      this.selectedUser=user;
      let idx=this.weekDays.indexOf(dayText); if(idx===-1) idx=0;
      let d=new Date(this.startDate); d.setDate(d.getDate()+idx);
      this.selectedDate=this.formatDate(d);
      this.selectedDates=[this.formatDateShort(d)];
      this.description='';
      this.modalOpen=true;
    },
    closeModal(){
      this.modalOpen=false; this.isEditing=false; this.editingEventId=null;
    },

    /* ---------------- utils ---------------- */
    truncate(t){ return !t?'': (t.length>12? t.substring(0,13)+'…' : t); },
    getEvent(user,dayShort){
      return this.events.find(e=>{
        let nombre=(typeof e.usuario==='object' && e.usuario.nombre)? e.usuario.nombre : e.usuario;
        return nombre===user && e.fechaShort===dayShort;
      });
    },

    handleCellClick(user,dayShort){
      let event=this.getEvent(user,dayShort);
      if(event){
        let descriptionItems = event.descripcion.split('.').map(item => item.trim()).filter(item => item.length > 0);
        let descriptionHtml = descriptionItems.map(item => `• ${item}`).join('<br>');
        let alignedDescriptionHtml = `<div style="text-align: left;">${descriptionHtml}</div>`;
        
        if(user===this.sessionUser){
          Swal.fire({
            title: 'Detalle Agenda',
            html: `<strong>Descripción:</strong><br>${alignedDescriptionHtml}`,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Editar',
            denyButtonText: 'Eliminar'
          }).then(r=>{
            if(r.isConfirmed) this.editEvent(event);
            else if(r.isDenied) this.deleteEvent(event.id);
          });
        }else{
          Swal.fire({
            title: 'Detalle Agenda',
            html: `<strong>Descripción:</strong><br>${alignedDescriptionHtml}`,
            icon: 'info'
          });
        }
      }else{
        if(user===this.sessionUser) this.openModal(user,dayShort);
        else Swal.fire('No hay eventos','','info');
      }
    },

    /* ======================= CRUD ======================= */
    /** --------------- CREAR --------------- */
    saveEvent(){
      if(this.description.trim().length===0){
        Swal.fire('Descripción requerida','Por favor ingrese la descripción del evento','warning');
        return;
      }

      Swal.fire({
        title:'Enviando Correo…',
        html:'<div class="flex justify-center items-center"><div class="loader"></div></div>',
        allowOutsideClick:false, showConfirmButton:false, background:'#f9f9f9',
        customClass:{ popup:'p-6 rounded-lg shadow-lg' }
      });

      let promises=this.selectedDates.map(dayFull=>{
        return fetch("{{ route('eventos.store') }}",{
          method:'POST',
          headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
          body:JSON.stringify({ usuario:this.selectedUser, fecha:dayFull, descripcion:this.description })
        }).then(r=>r.json());
      });

      Promise.all(promises).then(()=>{
        Swal.close(); this.fetchEvents(); this.modalOpen=false;
        Swal.fire('Evento(s) guardado(s)','','success');
      }).catch(err=>{ Swal.close(); console.error(err); });
    },

    /** --------------- EDITAR --------------- */
    editEvent(e){
      this.isEditing=true; this.editingEventId=e.id; this.selectedUser=e.usuario.nombre;
      this.selectedDate=e.fechaShort;
      this.selectedDates=[e.fechaShort]; this.description=e.descripcion; this.modalOpen=true;
    },

    updateEvent(){
      if(this.description.trim().length===0){
        Swal.fire('Descripción requerida','Por favor ingrese la descripción del evento','warning');
        return;
      }

      let url="{{ route('eventos.update',['id'=>'__id__']) }}".replace('__id__',this.editingEventId);
      fetch(url,{
        method:'PUT',
        headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body:JSON.stringify({ descripcion:this.description })
      }).then(r=>r.json()).then(data=>{
        if(data.error){ Swal.fire('Error',data.error,'error'); return; }
        let idx=this.events.findIndex(e=>e.id===this.editingEventId);
        if(idx!==-1) this.events[idx].descripcion=this.description;

        let original=this.selectedDates[0];
        let extraPromises=[];
        this.selectedDates.forEach(ds=>{
          if(ds!==original){
            extraPromises.push(
              fetch("{{ route('eventos.store') }}",{
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:JSON.stringify({ usuario:this.selectedUser, fecha:ds, descripcion:this.description })
              }).then(r=>r.json())
            );
          }
        });
        return Promise.all(extraPromises);
      }).then(()=>{
        this.fetchEvents(); this.modalOpen=false; this.isEditing=false; this.editingEventId=null;
        Swal.fire('Evento actualizado','','success');
      }).catch(console.error);
    },

    /** --------------- ELIMINAR --------------- */
    deleteEvent(id){
      Swal.fire({title:'¿Eliminar evento?',text:'Esta acción no se puede deshacer.',icon:'warning',
        showCancelButton:true,confirmButtonText:'Eliminar'})
      .then(r=>{
        if(!r.isConfirmed) return;
        let url="{{ route('eventos.destroy',['id'=>'__id__']) }}".replace('__id__',id);
        fetch(url,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' }})
          .then(r=>r.json()).then(()=>{
            this.events=this.events.filter(e=>e.id!==id);
            Swal.fire('Evento eliminado','','success');
          }).catch(console.error);
      });
    }
  }
}

function getMonday(date){
  let d=new Date(date); let day=d.getDay();
  d.setDate(d.getDate()-day+(day===0?-6:1)); return d;
}
</script>
@endsection