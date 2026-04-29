// Flash Learning — Auth & Session Management
const API_BASE = 'https://flashlearn-backend-production.up.railway.app';


// ── Session ────────────────────────────────────────────────
function getCurrentUser() { return JSON.parse(sessionStorage.getItem('fl_current_user') || 'null'); }
function setCurrentUser(u) { sessionStorage.setItem('fl_current_user', JSON.stringify(u)); }
function logout() { sessionStorage.removeItem('fl_current_user'); window.location.href = 'index.html'; }

function requireAuth(expectedRole) {
    const user = getCurrentUser();
    if (!user) { window.location.href = 'login.html'; return null; }
    if (expectedRole && user.role !== expectedRole) { alert('Access denied.'); redirectToDashboard(user.role); return null; }
    return user;
}
function redirectToDashboard(role) {
    if (role === 'admin') window.location.href = 'dashboard-admin.html';
    else if (role === 'tutor') window.location.href = 'dashboard-tutor.html';
    else window.location.href = 'dashboard-student.html';
}

// ── Auth ───────────────────────────────────────────────────
async function handleLogin(email, password) {
    const errEl = document.getElementById('loginError');
    errEl.classList.add('d-none');
    try {
        const res  = await fetch(`${API_BASE}/login.php`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({email,password}) });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); }
        catch { errEl.textContent = 'Server error: ' + text.substring(0, 200); errEl.classList.remove('d-none'); return; }
        if (data.success) { setCurrentUser(data.user); redirectToDashboard(data.user.role); }
        else { errEl.textContent = data.message || 'Invalid email or password.'; errEl.classList.remove('d-none'); }
    } catch { errEl.textContent = 'Cannot reach server. Please try again.'; errEl.classList.remove('d-none'); }
}

async function handleSignup(name, email, password, role) {
    const errEl = document.getElementById('signupError');
    errEl.classList.add('d-none');
    try {
        const res  = await fetch(`${API_BASE}/signup.php`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({name,email,password,role}) });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); }
        catch { errEl.textContent = 'Server error: ' + text.substring(0, 200); errEl.classList.remove('d-none'); return; }
        if (data.success) { setCurrentUser(data.user); redirectToDashboard(data.user.role); }
        else { errEl.textContent = data.message || 'Registration failed.'; errEl.classList.remove('d-none'); }
    } catch { errEl.textContent = 'Cannot reach server. Please try again.'; errEl.classList.remove('d-none'); }
}

// ── Users ──────────────────────────────────────────────────
async function getUsers() {
    try { const r = await fetch(`${API_BASE}/users.php`); const d = await r.json(); return d.success ? d.users : []; } catch { return []; }
}
async function deleteUser(id) {
    if (!confirm('Remove this user?')) return;
    try { const r = await fetch(`${API_BASE}/users.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'delete',id})}); const d = await r.json(); if(d.success) loadUsers(); } catch { alert('Error.'); }
}

// ── Tutors ─────────────────────────────────────────────────
async function getTutors() {
    try { const r = await fetch(`${API_BASE}/tutors.php`); const d = await r.json(); return d.success ? d.tutors : []; } catch { return []; }
}
async function getAllTutorsForChat() {
    try { const r = await fetch(`${API_BASE}/tutors.php?chat=1`); const d = await r.json(); return d.success ? d.tutors : []; } catch { return []; }
}

// ── Tutor Profile ──────────────────────────────────────────
async function getTutorProfile(userId) {
    try { const r = await fetch(`${API_BASE}/profile.php?user_id=${userId}`); const d = await r.json(); return d.profile || null; } catch { return null; }
}
async function saveTutorProfile(formData) {
    try { const r = await fetch(`${API_BASE}/profile.php`,{method:'POST',body:formData}); return await r.json(); } catch { return {success:false,message:'Server error.'}; }
}
async function adminProfileAction(payload) {
    try { const r = await fetch(`${API_BASE}/profile.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}); return await r.json(); } catch { return {success:false}; }
}

// ── Sessions ───────────────────────────────────────────────
async function getSessions(studentId, tutorId) {
    let url = `${API_BASE}/sessions.php`;
    if (studentId) url += `?student_id=${studentId}`;
    else if (tutorId) url += `?tutor_id=${tutorId}`;
    try { const r = await fetch(url); const d = await r.json(); return d.success ? d.sessions : []; } catch { return []; }
}
async function bookSession(payload) {
    try { const r = await fetch(`${API_BASE}/sessions.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'book',...payload})}); return await r.json(); } catch { return {success:false,message:'Server error.'}; }
}
async function updateSessionStatus(id, status, platform_link) {
    try { const r = await fetch(`${API_BASE}/sessions.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'update_status',id,status,platform_link:platform_link||''})}); return await r.json(); } catch { return {success:false}; }
}

// ── Materials ──────────────────────────────────────────────
async function getMaterials() {
    try { const r = await fetch(`${API_BASE}/materials.php`); const d = await r.json(); return d.success ? d.materials : []; } catch { return []; }
}
async function deleteMaterial(id) {
    try { const r = await fetch(`${API_BASE}/materials.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'delete',id})}); return await r.json(); } catch { return {success:false}; }
}

// ── Messages ───────────────────────────────────────────────
async function getConversation(myId, otherId) {
    try { const r = await fetch(`${API_BASE}/messages.php?sender_id=${myId}&receiver_id=${otherId}`); const d = await r.json(); return d.success ? d.messages : []; } catch { return []; }
}
async function getConversations(userId) {
    try { const r = await fetch(`${API_BASE}/messages.php?user_id=${userId}`); const d = await r.json(); return d.success ? d.conversations : []; } catch { return []; }
}
async function sendMessage(sender_id, receiver_id, message) {
    try { const r = await fetch(`${API_BASE}/messages.php`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'send',sender_id,receiver_id,message})}); return await r.json(); } catch { return {success:false}; }
}
async function getAllMessages() {
    try { const r = await fetch(`${API_BASE}/messages.php?all=1`); const d = await r.json(); return d.success ? d.messages : []; } catch { return []; }
}
