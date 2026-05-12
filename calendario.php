<?php include "plantilla.php"; ?>

<style>
/* --- ESTILO DE CALENDARIO TIPO COMPUTADOR --- */
#calendar-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

#calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
    margin-top: 10px;
}

.day, .day-header {
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
}

.day-header {
    background: #e3e9f0;
    font-weight: bold;
}

.day {
    background: #f1f5f9;
    border: 1px solid #d0d7de;
    cursor: pointer;
}
.day:hover {
    background: #dbe5ee;
}

.day-marker {
    width: 8px;
    height: 8px;
    background: #0d47a1;
    border-radius: 50%;
    margin: 6px auto 0;
}

/* Panel derecho */
.card {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

#upcoming {
    max-height: 380px;
    overflow-y: auto;
}

</style>

<div style="display: grid; grid-template-columns: 1fr 330px; gap: 20px;">

    <!-- CALENDARIO -->
    <div id="calendar-container">
        <h2 style="margin-bottom: 10px;">Calendario de Citas</h2>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <button onclick="prevMonth()">◀</button>
            <h3 id="monthTitle"></h3>
            <button onclick="nextMonth()">▶</button>
        </div>

        <div id="calendar-grid"></div>
    </div>

    <!-- PANEL DERECHO -->
    <aside>
        <div class="card">
            <h3>Citas Próximas</h3>
            <div id="upcoming">Cargando...</div>
        </div>

        <div class="card" style="margin-top:15px;">
            <h3>Citas del Día</h3>
            <div id="day-citas">Seleccione un día</div>
        </div>
    </aside>

</div>

<script>
let citas = [];
let viewYear, viewMonth;

/* ============================================
   CARGAR CITAS DESDE PHP
   ============================================ */
fetch("get_citas.php")
    .then(r => r.json())
    .then(data => {
        citas = data;

        /* 🔥 NORMALIZAR FECHAS A YYYY-MM-DD */
        citas = citas.map(c => {
            let f = c.fecha.trim();

            if (f.includes(" ")) f = f.split(" ")[0]; // quitar hora

            if (f.includes("/")) {              // dd/mm/yyyy → yyyy-mm-dd
                let [d, m, y] = f.split("/");
                f = `${y}-${m.padStart(2,"0")}-${d.padStart(2,"0")}`;
            } 
            else if (f.split("-")[0].length == 2) {  // dd-mm-yyyy → yyyy-mm-dd
                let [d, m, y] = f.split("-");
                f = `${y}-${m.padStart(2,"0")}-${d.padStart(2,"0")}`;
            }

            let [y, m, d] = f.split("-");
            f = `${y}-${m.padStart(2,"0")}-${d.padStart(2,"0")}`;

            return {...c, fecha: f};
        });

        const today = new Date();
        viewYear = today.getFullYear();
        viewMonth = today.getMonth();

        loadCalendar();
        loadUpcoming();
    });

/* ============================================
   CITAS PRÓXIMAS
   ============================================ */
function loadUpcoming() {
    const cont = document.getElementById("upcoming");
    cont.innerHTML = "";

    if (!citas.length)
        return cont.innerHTML = "No hay citas registradas.";

    citas.forEach(c => {
        let div = document.createElement("div");
        div.style.padding = "6px 0";
        div.innerHTML = `
            <strong>${c.fecha}</strong> — ${c.hora}<br>
            ${c.paciente} con ${c.medico}
        `;
        cont.appendChild(div);
    });
}

/* ============================================
   GENERAR CALENDARIO
   ============================================ */
function loadCalendar() {
    const grid = document.getElementById("calendar-grid");
    grid.innerHTML = "";

    const monthTitle = document.getElementById("monthTitle");
    const months = [
        "Enero","Febrero","Marzo","Abril","Mayo","Junio",
        "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"
    ];

    monthTitle.textContent = `${months[viewMonth]} ${viewYear}`;

    const firstDay = new Date(viewYear, viewMonth, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth+1, 0).getDate();

    const dayNames = ["D","L","M","M","J","V","S"];

    dayNames.forEach(d => {
        let h = document.createElement("div");
        h.className = "day-header";
        h.textContent = d;
        grid.appendChild(h);
    });

    for (let i = 0; i < firstDay; i++) {
        let empty = document.createElement("div");
        grid.appendChild(empty);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        let c = document.createElement("div");
        c.className = "day";
        c.textContent = d;

        let date = `${viewYear}-${String(viewMonth+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;

        if (citas.some(ci => ci.fecha === date)) {
            let marker = document.createElement("div");
            marker.className = "day-marker";
            c.appendChild(marker);
        }

        c.onclick = () => showDay(date);
        grid.appendChild(c);
    }
}

/* ============================================
   CAMBIO DE MES
   ============================================ */
function prevMonth(){
    viewMonth--;
    if (viewMonth < 0){ viewMonth = 11; viewYear--; }
    loadCalendar();
}

function nextMonth(){
    viewMonth++;
    if (viewMonth > 11){ viewMonth = 0; viewYear++; }
    loadCalendar();
}

/* ============================================
   MOSTRAR CITAS DEL DÍA
   ============================================ */
function showDay(date){
    const cont = document.getElementById("day-citas");
    cont.innerHTML = "";

    const list = citas.filter(c => c.fecha === date);

    if (!list.length)
        return cont.textContent = "No hay citas para este día.";

    list.forEach(c => {
        let p = document.createElement("p");
        p.innerHTML = `
            <strong>${c.hora}</strong> — 
            ${c.paciente} (Dr. ${c.medico})
        `;
        cont.appendChild(p);
    });
}
</script>

<?php include "footer.php"; ?>