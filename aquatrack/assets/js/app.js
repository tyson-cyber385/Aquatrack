/* AquaTrack front-end only. All data below is sample data; replace with backend calls. */
const $=(s,r=document)=>r.querySelector(s), $$=(s,r=document)=>[...r.querySelectorAll(s)];
const peso=n=>'₱'+Number(n).toLocaleString('en-PH',{minimumFractionDigits:2});
const ic=n=>`<i data-eva="${n}"></i>`;
const PILL={Delivered:'ok',Paid:'ok',Approved:'ok','Out for Delivery':'info',Pending:'warn',Partial:'warn',Unpaid:'bad',Cancelled:'gray',Rejected:'bad',Refill:'info','New gallon':'ok',Dispenser:'warn'};
const pill=t=>`<span class="pill ${PILL[t]||'gray'}">${t}</span>`;

const D={
 customers:[['C-001','Juan Dela Cruz','0917 555 1234','Purok 3, Brgy. Poblacion',3],['C-002','Maria Santos','0918 222 8841','12 Rizal St.',1],['C-003','Pedro Reyes','0922 410 7765','Blk 5 Lot 8, Greenfields',2],['C-004','Ana Villanueva','0905 118 3320','45 Mabini Ave.',0],['C-005','Carlo Mendoza','0933 907 6612','Sitio Riverside',4]],
 products:[['P-01','Refill (5 gal)','Refill',25,320,50],['P-02','New Gallon (Slim)','New gallon',180,14,20],['P-03','New Gallon (Round)','New gallon',150,6,20],['P-04','Dispenser (Hot & Cold)','Dispenser',3200,3,5]],
 orders:[['#1042','Juan Dela Cruz','Out for Delivery','Unpaid',250,'Refill x10'],['#1041','Maria Santos','Delivered','Paid',125,'Refill x5'],['#1040','Pedro Reyes','Pending','Partial',1700,'New Gallon x5, Refill x4'],['#1039','Ana Villanueva','Delivered','Paid',75,'Refill x3'],['#1038','Carlo Mendoza','Pending','Unpaid',450,'Refill x18']],
 payments:[['PAY-310','#1041','Maria Santos',125,'Cash','Today 9:40 AM'],['PAY-309','#1039','Ana Villanueva',75,'Cash','Today 8:15 AM'],['PAY-308','#1040','Pedro Reyes',700,'GCash','Yesterday']],
 staff:[['Liza Ramos','Cashier','8:00 AM','Online',12],['Mark Tan','Delivery coordinator','8:30 AM','Online',9],['Joy Cruz','Cashier','—','Offline',0]],
 week:[['Fri',3100],['Sat',3900],['Sun',2700],['Mon',3300],['Tue',4100],['Wed',3600],['Thu',4250]],
 msgs:[['staff','Stock of Round gallons is down to 6. Can we restock?','9:02 AM'],['admin','Noted. I will order 30 more. Approved.','9:10 AM']],
 notifs:[['message-circle-outline','Admin approved your restock request','5 min ago'],['shopping-bag-outline','New order #1042 placed','20 min ago'],['credit-card-outline','Payment recorded for #1041','1 hr ago']]
};
const USERS={admin:{name:'System Admin',ini:'SA',greet:'Admin'},staff:{name:'System Staff',ini:'ST',greet:'Staff'},customer:{name:'Juan Dela Cruz',ini:'JD',greet:'Juan Dela Cruz'}};
const NAV={
 admin:[['dashboard','Dashboard','grid-outline'],['customers','Customers','people-outline'],['products','Products','cube-outline'],['orders','Orders','shopping-bag-outline'],['deliveries','Deliveries','car-outline'],['balances','Balances','credit-card-outline'],['reports','Reports','bar-chart-outline'],['people','People','person-done-outline'],['messages','Messages','message-circle-outline'],['settings','Settings','settings-2-outline']],
 staff:[['dashboard','Dashboard','grid-outline'],['customers','Customers','people-outline'],['products','Products','cube-outline'],['orders','Orders','shopping-bag-outline'],['deliveries','Deliveries','car-outline'],['balances','Balances','credit-card-outline'],['reports','Reports','bar-chart-outline'],['messages','Messages','message-circle-outline'],['settings','Settings','settings-2-outline']],
 customer:[['dashboard','Dashboard','grid-outline'],['neworder','New Order','plus-circle-outline'],['orders','My Orders','shopping-bag-outline'],['deliveries','Track Delivery','car-outline'],['balances','Balances / Payments','credit-card-outline'],['containers','My Containers','archive-outline'],['notifications','Notifications','bell-outline'],['settings','Profile','person-outline']]
};
let role='admin', chosen='admin';

/* ---------- helpers ---------- */
const icons=()=>eva.replace();
function toast(msg,type='success'){
  const c=type==='success'?'#16a34a':'#dc2626',i=type==='success'?'checkmark-circle-2-outline':'alert-circle-outline';
  const el=document.createElement('div');el.className='toast align-items-center show';
  el.innerHTML=`<div class="d-flex"><div class="toast-body d-flex gap-2 align-items-center"><span style="color:${c}">${ic(i)}</span>${msg}</div><button class="btn-close me-3 m-auto" data-bs-dismiss="toast"></button></div>`;
  $('#toasts').append(el);icons();setTimeout(()=>el.remove(),3500);
}
const modal=id=>bootstrap.Modal.getOrCreateInstance($(id));
let okMsg='';
function openForm(title,fields,msg){
  $('#fmTitle').textContent=title;okMsg=msg;$('#fm').classList.remove('was-validated');
  $('#fmBody').innerHTML=fields.map(f=>`<div class="mb-3"><label class="form-label">${f[0]}</label>${f[2]?`<select class="form-select" required>${f[2].map(o=>`<option>${o}</option>`).join('')}</select>`:`<input class="form-control" type="${f[1]||'text'}" required>`}<div class="invalid-feedback">Invalid input</div></div>`).join('');
  modal('#formModal').show();
}
$('#fm').addEventListener('submit',e=>{e.preventDefault();const f=e.target;f.classList.add('was-validated');
  if(!f.checkValidity()){toast('Invalid input','error');return}modal('#formModal').hide();toast(okMsg)});
let cfAction=null;
function confirmBox(title,text,btn,msg){$('#cfTitle').textContent=title;$('#cfText').textContent=text;$('#cfOk').textContent=btn;cfAction=msg;icons();modal('#confirmModal').show()}
$('#cfOk').onclick=()=>{modal('#confirmModal').hide();toast(cfAction)};

const head=(t,s,btn='')=>`<div class="page-head"><div><h4>${t}</h4><div class="muted small">${s}</div></div>${btn}</div>`;
const addBtn=(label,id)=>`<button class="btn btn-brand" data-act="${id}">${ic('plus-outline')} ${label}</button>`;
const card=(t,s,body,extra='')=>`<div class="card-x ${extra}"><h6>${t}</h6><div class="sub">${s||''}</div>${body}</div>`;
const stat=(c,i,l,v)=>`<div class="stat"><span class="ico ${c}">${ic(i)}</span><div><small>${l}</small><strong>${v}</strong></div></div>`;
const table=(h,rows)=>`<div class="table-wrap"><table class="table"><thead><tr>${h.map(x=>`<th${x==='Total'||x==='Amount'?' class="text-end"':''}>${x}</th>`).join('')}</tr></thead><tbody>${rows.map(r=>`<tr>${r.map(c=>`<td>${c}</td>`).join('')}</tr>`).join('')}</tbody></table></div>`;
const acts=(...a)=>a.map(([i,k,t,cls])=>`<button class="btn btn-sm ${cls||'btn-soft'} me-1" data-act="${k}" title="${t}">${ic(i)}</button>`).join('');
const del=(what)=>['trash-2-outline','del-'+what,'Delete','btn-outline-danger'];
const toolbar=(ph,extra='')=>`<div class="toolbar"><div class="search flex-grow-1" style="max-width:320px"><i data-eva="search-outline"></i><input class="form-control" style="padding-left:2.6rem" data-filter placeholder="${ph}"></div>${extra}</div>`;
const sel=(o)=>`<select class="form-select" data-filter-sel><option value="">${o[0]}</option>${o.slice(1).map(x=>`<option>${x}</option>`).join('')}</select>`;
const bars=()=>{const m=4250;return `<div class="bars">${D.week.map((w,i)=>`<div class="${i==6?'on':''}"><span style="height:${w[1]/m*100}%" title="${peso(w[1])}"></span></div>`).join('')}</div><div class="bar-lbl">${D.week.map(w=>`<span>${w[0]}</span>`).join('')}</div>`};
const ordRows=(n=5)=>D.orders.slice(0,n).map(o=>[o[0],o[1],pill(o[2]),pill(o[3]),`<div class="text-end">${peso(o[4])}</div>`]);
const recent=()=>card('Recent Orders','Latest activity across walk-in and delivery',table(['Order ID','Customer Name','Delivery Status','Payment Status','Total'],ordRows()));
const payList=()=>card('Recent Payments','Latest payment records',D.payments.map(p=>`<div class="list-row"><div><strong>${p[2]}</strong><div class="muted small">${p[1]} · ${p[4]} · ${p[5]}</div></div><strong>${peso(p[3])}</strong></div>`).join(''));
const containers=(b,r)=>card('Container Summary','Borrowed vs returned',`<div class="list-row"><span>Borrowed</span><strong>${b}</strong></div><div class="list-row"><span>Returned</span><strong>${r}</strong></div><div class="prog mt-2"><i style="width:${r/(b+r)*100}%"></i></div>`);
const timeline=(step)=>`<ul class="timeline">${[['Order Placed','Order #1042 received','file-text-outline'],['Pending','Waiting for dispatch','clock-outline'],['Out for Delivery','On the way to your address','car-outline'],['Delivered','Gallons received','checkmark-circle-2-outline']].map((s,i)=>`<li class="${i<=step?'done':''}"><span class="dot">${ic(s[2])}</span><div><strong>${s[0]}</strong><div class="small">${s[1]}</div></div></li>`).join('')}</ul>`;
const requests=(admin)=>card('Restock Requests','Pending staff requests',[['Liza Ramos','New Gallon (Round) · 6 left'],['Mark Tan','Refill water · 40 gal buffer']].map(r=>`<div class="list-row"><div><strong>${r[0]}</strong><div class="muted small">${r[1]}</div></div>${admin?`<div>${acts(['checkmark-outline','approve','Approve','btn-soft'],['close-outline','reject','Reject','btn-outline-danger'])}</div>`:pill('Pending')}</div>`).join(''));
const chat=()=>card('Restock Conversation','Staff and admin stock messages',`<div class="chat"><div class="chat-log" id="chatLog">${D.msgs.map(m=>msgHtml(m)).join('')}</div><div class="chat-in"><input class="form-control" id="chatIn" placeholder="Type a message about stock"><button class="btn btn-brand" data-act="send">${ic('paper-plane-outline')}</button></div></div>`);
const msgHtml=m=>`<div class="msg ${m[0]===role||(role==='admin'&&m[0]==='admin')?'me':''}"><small>${m[0]==='admin'?'System Admin':'System Staff'} · ${m[2]}</small>${m[1]}</div>`;

/* ---------- pages ---------- */
const P={
 dashboard(){
  const g=USERS[role].greet;
  const banner=`<div class="banner"><h3>Welcome back, ${g}</h3><p>${role==='customer'?'Here is your AquaTrack summary.':'Here is what’s happening at your station today.'}</p></div>`;
  if(role==='admin')return banner+`<div class="stats">${stat('blue','shopping-cart-outline','Today’s Sales','₱4,250')}${stat('amber','car-outline','Pending Deliveries','12')}${stat('red','credit-card-outline','Unpaid Balances','₱1,800')}${stat('green','people-outline','Total Customers','145')}${stat('violet','alert-triangle-outline','Low Stock Alerts','2')}${stat('teal','person-done-outline','Staff On Shift','2')}</div>
   <div class="grid-2">${card('Weekly Sales Overview','Sales for the last 7 days (₱)',bars())}${recent()}</div>
   <div class="grid-eq">${requests(true)}${payList()}${card('Top Products','Most ordered this week',D.products.slice(0,3).map((p,i)=>`<div class="list-row"><span>${p[1]}</span><strong>${[182,41,23][i]} orders</strong></div>`).join(''))}${containers(38,31)}</div>`;
  if(role==='staff')return banner+`<div class="stats">${stat('blue','shopping-cart-outline','Today’s Sales','₱2,150')}${stat('amber','car-outline','Pending Deliveries','12')}${stat('red','credit-card-outline','Unpaid Balances','₱1,800')}${stat('green','people-outline','Total Customers','145')}</div>
   <div class="grid-2">${card('Delivery Queue','Pending or out for delivery',table(['Order','Customer','Status'],D.orders.filter(o=>o[2]!=='Delivered').map(o=>[o[0],o[1],pill(o[2])])))}${card('Today’s Orders','Orders created today',table(['Order ID','Customer Name','Delivery Status','Payment Status','Total'],ordRows()))}</div>
   <div class="grid-eq">${card('Payment Summary','Paid vs unpaid today',`<div class="list-row"><span>Paid orders</span><strong>8</strong></div><div class="list-row"><span>Unpaid orders</span><strong>3</strong></div><div class="prog mt-2"><i style="width:72%"></i></div>`)}${card('Low Stock Requests','Products needing restock',D.products.filter(p=>p[4]<=p[5]).map(p=>`<div class="list-row"><div><strong>${p[1]}</strong><div class="muted small">${p[4]} left</div></div><button class="btn btn-sm btn-brand" data-act="request">Send request</button></div>`).join('')||'<div class="muted">Nothing low.</div>')}${card('Recent Customers','Latest registered',D.customers.slice(0,4).map(c=>`<div class="list-row"><span>${c[1]}</span><span class="muted small">${c[2]}</span></div>`).join(''))}</div>`;
  return banner+`<div class="stats">${stat('blue','clock-outline','Active Orders','2')}${stat('amber','car-outline','Next Delivery Status','Out for Delivery')}${stat('red','credit-card-outline','Unpaid Balance','₱250.00')}${stat('green','archive-outline','Containers Borrowed','3')}</div>
   <div class="grid-2">${card('Track Delivery','Latest order #1042',timeline(2))}${card('My Recent Orders','',table(['Order ID','Date','Items','Total','Payment','Delivery'],D.orders.slice(0,3).map(o=>[o[0],'Oct 31',o[5],peso(o[4]),pill(o[3]),pill(o[2])])))}</div>
   <div class="grid-eq">${card('Payment History','Recent payments',D.payments.slice(0,2).map(p=>`<div class="list-row"><span>${p[1]} · ${p[5]}</span><strong>${peso(p[3])}</strong></div>`).join(''))}${containers(3,5)}${card('Notifications','Order and payment updates',D.notifs.map(n=>`<div class="list-row"><span>${n[1]}</span><span class="muted small">${n[2]}</span></div>`).join(''))}</div>`;
 },
 customers(){
  const a=role==='admin';
  return head('Customers','Add, view, edit, and search customer records',addBtn('Add Customer','add-customer'))+card('','',toolbar('Search by name, contact, or address')+table(['ID','Name','Contact','Address','Containers','Actions'],D.customers.map(c=>[c[0],`<strong>${c[1]}</strong>`,c[2],c[3],c[4],acts(['eye-outline','view','View'],['edit-2-outline','edit','Edit'],...(a?[del('customer')]:[]))])));
 },
 products(){
  const a=role==='admin';
  return head('Products','Refill, new gallon, and dispenser stock',a?addBtn('Add Product','add-product'):'')+`<div class="stats">${stat('green','cube-outline','Products Available',D.products.length)}${stat('red','alert-triangle-outline','Low Stock',D.products.filter(p=>p[4]<=p[5]).length)}</div>`+
  `<div class="grid-2" style="grid-template-columns:minmax(0,7fr) minmax(0,5fr)">${card('','',toolbar('Search products')+table(['ID','Product','Type','Price','Stock','Actions'],D.products.map(p=>{const low=p[4]<=p[5];return [p[0],`<strong>${p[1]}</strong>`,pill(p[2]),peso(p[3]),`${p[4]}<div class="prog ${low?'low':''} mt-1" style="width:80px"><i style="width:${Math.min(100,p[4]/(p[5]*3)*100)}%"></i></div>`,a?acts(['edit-2-outline','edit','Edit'],['plus-circle-outline','restock','Restock','btn-brand'],del('product')):(low?`<button class="btn btn-sm btn-brand" data-act="request">Request</button>`:'<span class="muted">—</span>')]}))) }${chat()}</div>`;
 },
 orders(){
  const c=role==='customer';
  return head(c?'My Orders':'Orders','Pending → Out for Delivery → Delivered',addBtn('Create Order','add-order'))+card('','',toolbar('Search by customer, order ID, or status',sel(['All statuses','Pending','Out for Delivery','Delivered','Cancelled']))+table(['Order ID','Customer','Items','Delivery','Payment','Total','Actions'],D.orders.map(o=>[o[0],o[1],o[5],pill(o[2]),pill(o[3]),peso(o[4]),acts(['eye-outline','view','View'],...(o[2]==='Pending'?[['close-circle-outline','cancel','Cancel','btn-outline-danger']]:[]),...(c?[['refresh-outline','reorder','Reorder']]:[del('order')]))])));
 },
 neworder(){
  return head('New Order','Choose a product and delivery details')+card('Order Details','',`<form id="orderForm" novalidate class="row g-3"><div class="col-md-6"><label class="form-label">Product</label><select class="form-select" required>${D.products.map(p=>`<option>${p[1]} · ${peso(p[3])}</option>`).join('')}</select></div><div class="col-md-6"><label class="form-label">Quantity</label><input type="number" min="1" class="form-control" required></div><div class="col-md-6"><label class="form-label">Service</label><select class="form-select"><option>Delivery</option><option>Pickup</option></select></div><div class="col-md-6"><label class="form-label">Preferred date / time</label><input type="datetime-local" class="form-control" required></div><div class="col-12"><label class="form-label">Address</label><input class="form-control" required></div><div class="col-12"><button class="btn btn-brand">${ic('checkmark-outline')} Place Order</button></div></form>`);
 },
 deliveries(){
  const c=role==='customer';
  const next={Pending:'Out for Delivery','Out for Delivery':'Delivered'};
  if(c)return head('Track Delivery','Your latest order')+card('Order #1042','Refill x10 · Out for Delivery',timeline(2));
  return head('Deliveries','Updated by staff when the delivery person reports back')+`<div class="stats">${stat('amber','clock-outline','Pending',2)}${stat('blue','car-outline','Out for Delivery',1)}${stat('green','checkmark-circle-2-outline','Delivered',2)}</div>`+card('','',toolbar('Search deliveries',sel(['All statuses','Pending','Out for Delivery','Delivered','Cancelled']))+table(['Order','Customer','Address','Status','Update'],D.orders.map((o,i)=>[o[0],o[1],D.customers[i][3],pill(o[2]),next[o[2]]?`<button class="btn btn-sm btn-soft" data-act="advance">${ic('arrow-forward-outline')} Mark ${next[o[2]]}</button>`:'<span class="muted">Completed</span>'])));
 },
 balances(){
  const c=role==='customer';
  return head(c?'Balances / Payments':'Balances','Payments and unpaid balances',c?'':addBtn('Record Payment','add-payment'))+`<div class="stats">${stat('red','credit-card-outline','Unpaid Balance',c?'₱250.00':'₱1,800.00')}${stat('green','checkmark-circle-2-outline','Paid Today','₱200.00')}</div>`+card('Payment History','',table(['Payment ID','Order','Customer','Amount','Method','Date'],D.payments.map(p=>[p[0],p[1],p[2],peso(p[3]),p[4],p[5]])));
 },
 reports(){
  const a=role==='admin';
  const list=a?['Total sales','Sales today','Sales by date range','Sales by product','Sales by staff','Unpaid balances','Payment history','Whole sales history','Container aging','Top customers']:['Daily sales summary','Payment status summary','Unpaid balances summary'];
  return head('Reports',a?'Detailed station reports':'Limited daily summaries',a?`<div><button class="btn btn-soft me-2">${ic('download-outline')} CSV</button><button class="btn btn-brand">${ic('download-outline')} PDF</button></div>`:'')+`<div class="stats">${stat('blue','trending-up-outline','Total Sales','₱186,420')}${stat('green','shopping-cart-outline','Sales Today','₱4,250')}${stat('red','credit-card-outline','Unpaid','₱1,800')}</div><div class="grid-2">${card('Sales Overview','Last 7 days (₱)',bars())}${card('Available Reports','',list.map(l=>`<div class="list-row"><span>${ic('file-text-outline')} ${l}</span><button class="btn btn-sm btn-soft">View</button></div>`).join(''))}</div>`+(a?card('Sales by Staff','Today',table(['Staff','Orders','Total'],D.staff.map(s=>[s[0],s[4],peso(s[4]*180)]))):'');
 },
 people(){
  return head('People','Staff currently on shift',addBtn('Add Staff','add-staff'))+card('','',table(['Staff','Role','Clock-in','Status','Orders Today','Actions'],D.staff.map(s=>[`<strong>${s[0]}</strong>`,s[1],s[2],`<span class="online ${s[3]==='Online'?'':'off'}"></span>${s[3]}`,s[4],acts(['edit-2-outline','edit','Edit'],del('staff'))])));
 },
 messages(){return head('Messages','Staff and admin conversation')+chat()},
 notifications(){return head('Notifications','Order and payment updates')+card('','',D.notifs.map(n=>`<div class="list-row"><span class="d-flex gap-3 align-items-center"><span class="ico blue">${ic(n[0])}</span>${n[1]}</span><span class="muted small">${n[2]}</span></div>`).join(''))},
 containers(){return head('My Containers','Borrowed and returned gallons')+`<div class="stats">${stat('amber','archive-outline','Borrowed',3)}${stat('green','checkmark-circle-2-outline','Returned',5)}</div>`+card('Container Log','',table(['Date','Order','Borrowed','Returned'],[['Oct 31','#1042',2,0],['Oct 28','#1035',0,2],['Oct 20','#1020',1,3]]))},
 settings(){
  const a=role==='admin';
  return head(role==='customer'?'Profile':'Settings',a?'Station and system configuration':'Manage your account')+`<div class="grid-eq">${card('Profile','',`<form class="row g-3" id="setForm" novalidate><div class="col-12"><label class="form-label">Full name</label><input class="form-control" value="${USERS[role].name}" required></div><div class="col-12"><label class="form-label">Contact number</label><input class="form-control" required></div><div class="col-12"><label class="form-label">New password</label><input type="password" class="form-control"></div><div class="col-12"><button class="btn btn-brand">Save changes</button></div></form>`)}${a?card('Station Settings','',`<div class="list-row"><span>Station information</span>${ic('chevron-right-outline')}</div><div class="list-row"><span>Price list</span>${ic('chevron-right-outline')}</div><div class="list-row"><span>Low-stock threshold</span>${ic('chevron-right-outline')}</div><div class="list-row"><span>User management</span>${ic('chevron-right-outline')}</div><div class="list-row"><span>Backup options</span>${ic('chevron-right-outline')}</div><div class="list-row"><span>System information</span>${ic('chevron-right-outline')}</div>`):''}${role==='customer'?card('Notification Preferences','',['Order updates','Payment reminders','Delivery updates'].map(n=>`<div class="list-row"><span>${n}</span><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div></div>`).join('')):''}</div>`;
 }
};

/* ---------- routing ---------- */
function show(id){['front','login'].forEach(s=>$('#'+s).classList.toggle('d-none',s!==id));$('#app').classList.add('d-none');icons()}
function render(){
  const page=(location.hash||'#dashboard').slice(1);
  const items=NAV[role];const cur=items.find(i=>i[0]===page)?page:'dashboard';
  $('#nav').innerHTML=items.map(i=>`<a class="nav-link-x ${i[0]===cur?'active':''}" href="#${i[0]}">${ic(i[2])}${i[1]}${i[0]==='messages'?'<span class="badge-n">2</span>':''}</a>`).join('');
  $('#view').innerHTML=P[cur]();
  $('#userName').textContent=USERS[role].name;$('#avatar').textContent=USERS[role].ini;
  $('#notifMenu').innerHTML=`<div class="px-2 py-1 fw-bold">Notifications</div>`+D.notifs.map(n=>`<div class="n-item"><span class="ico blue" style="width:36px;height:36px">${ic(n[0])}</span><div>${n[1]}<br><small>${n[2]}</small></div></div>`).join('');
  $('#sidebar').classList.remove('open');$('#backdrop').classList.remove('show');
  icons();const cl=$('#chatLog');if(cl)cl.scrollTop=cl.scrollHeight;
}
function enter(){$('#front').classList.add('d-none');$('#login').classList.add('d-none');$('#app').classList.remove('d-none');location.hash='#dashboard';render()}
window.addEventListener('hashchange',()=>{if(!$('#app').classList.contains('d-none'))render()});

/* ---------- events ---------- */
document.addEventListener('click',e=>{
  const go=e.target.closest('[data-go]');if(go){show(go.dataset.go);return}
  const rt=e.target.closest('#roleTabs button');
  if(rt){$$('#roleTabs button').forEach(b=>b.classList.toggle('active',b===rt));chosen=rt.dataset.role;$('#lgBtn').textContent='Sign In as '+rt.textContent.trim();return}
  const a=e.target.closest('[data-act]');if(!a)return;const k=a.dataset.act;
  const F={
   'add-customer':()=>openForm('Add Customer',[['Name'],['Contact number','tel'],['Address']],'Customer added'),
   'add-product':()=>openForm('Add Product',[['Product name'],['Type','',['Refill','New gallon','Dispenser']],['Unit price','number'],['Stock quantity','number']],'Product added'),
   'add-order':()=>openForm('Create Order',[['Customer'],['Product','',D.products.map(p=>p[1])],['Quantity','number'],['Delivery address']],'Order placed'),
   'add-payment':()=>openForm('Record Payment',[['Order ID'],['Amount','number'],['Method','',['Cash','GCash']]],'Payment recorded'),
   'add-staff':()=>openForm('Add Staff',[['Full name'],['Username'],['Role','',['Cashier','Delivery coordinator']]],'Staff account created'),
   edit:()=>openForm('Edit Record',[['Name'],['Details']],'Record updated'),
   restock:()=>openForm('Restock Product',[['Quantity to add','number']],'Product restocked'),
   view:()=>toast('Opening details'),
   request:()=>toast('Restock request sent to Admin'),
   approve:()=>toast('Restock request approved'),reject:()=>toast('Restock request rejected','error'),
   advance:()=>toast('Order updated'),reorder:()=>toast('Items added to a new order'),
   cancel:()=>confirmBox('Cancel this order?','Only pending orders can be cancelled.','Cancel order','Order cancelled'),
   send:()=>{const i=$('#chatIn');if(!i.value.trim()){toast('Invalid input','error');return}$('#chatLog').insertAdjacentHTML('beforeend',msgHtml([role==='admin'?'admin':'staff',i.value,'Now']));i.value='';$('#chatLog').scrollTop=1e5}
  };
  if(k.startsWith('del-'))return confirmBox('Delete this '+k.slice(4)+'?','This action cannot be undone.','Delete',k.slice(4)[0].toUpperCase()+k.slice(5)+' deleted');
  F[k]&&F[k]();
});
document.addEventListener('input',e=>{
  if(e.target.matches('[data-filter]')){const q=e.target.value.toLowerCase();$$('#view tbody tr').forEach(r=>r.hidden=!r.textContent.toLowerCase().includes(q))}
});
document.addEventListener('change',e=>{
  if(e.target.matches('[data-filter-sel]')){const q=e.target.value.toLowerCase();$$('#view tbody tr').forEach(r=>r.hidden=!r.textContent.toLowerCase().includes(q))}
});
document.addEventListener('submit',e=>{
  if(e.target.matches('#orderForm,#setForm')){e.preventDefault();const f=e.target;f.classList.add('was-validated');f.checkValidity()?toast(f.id==='orderForm'?'Order placed':'Profile updated'):toast('Invalid input','error')}
});
$('#togglePass').onclick=()=>{const p=$('#lgPass'),s=p.type==='password';p.type=s?'text':'password';$('#togglePass').innerHTML=ic(s?'eye-off-outline':'eye-outline');icons()};
$('#loginForm').addEventListener('submit',e=>{e.preventDefault();
  if(!$('#lgUser').value.trim()||!$('#lgPass').value){toast('Invalid input','error');return}
  role=chosen;toast('Login successful');enter()});
$('#logoutBtn').onclick=()=>confirmBox('Log out?','Logging out ends your session.','Log out','You have been logged out');
$('#cfOk').addEventListener('click',()=>{if(cfAction==='You have been logged out'){location.hash='';show('login')}});
$('#menuBtn').onclick=()=>{$('#sidebar').classList.add('open');$('#backdrop').classList.add('show')};
$('#backdrop').onclick=()=>{$('#sidebar').classList.remove('open');$('#backdrop').classList.remove('show')};
$('#globalSearch').addEventListener('input',e=>{const q=e.target.value.toLowerCase();$$('#view tbody tr').forEach(r=>r.hidden=!r.textContent.toLowerCase().includes(q))});
show('front');
