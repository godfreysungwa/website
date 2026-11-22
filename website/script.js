// Complete Translation Dictionary (English / Swahili)
const TRANSLATIONS = {
  en: {
    nav_home: "Home",
    nav_products: "Products",
    nav_cart: "Order Cart",
    nav_mission: "Mission",
    nav_about: "About Us",
    footer_orders: "Direct Orders:",

    // Home Page
    hero_title: "Pure and Natural",
    hero_sub: "Premium quality milling and packaging of clean, stone free cereal flours in Arusha, Tanzania.",
    hero_btn: "Browse & Order Products",
    purpose_badge: "Our Purpose",
    purpose_title: "Nourishing Whole Families",
    purpose_desc: "Nyirefami Limited is dedicated to processing and packaging high quality cereal flours for all household income levels, with special emphasis on baby and family nutrition.",
    purpose_link: "Read Mission Statement →",
    essential_title: "Our Essential Products",
    ess_ulezi_desc: "Rich in natural calcium and iron, our top choice for energizing family porridge and nursing mothers.",
    ess_mtama_desc: "Finely milled from selected domestic grains, packed with dietary fiber for wholesome ugali and baking.",
    ess_sooji_desc: "Graded wheat semolina ideal for growing children aged 4+ months, soft cakes, and creamy desserts.",
    btn_order_ulezi: "Order Finger Millet Flour",
    btn_order_mtama: "Order Sorghum Flour",
    btn_order_sooji: "Order Wheat Grain Flour",
    exec_title: "Executive Leadership & Management",
    role_founder: "Founder",
    role_ceo: "CEO",
    role_md: "Managing Director",
    role_od: "Operations Director",
    hist_title: "Established in Arusha",
    hist_desc: "Originally established in 1981 as re-pack flour and re-registered in 1991 under Certificate No. 50656, we have over four decades of heritage in milling clean cereal products.",
    hist_link: "Read Full History →",
    qual_title: "Quality & Hygiene Vow",
    qual_desc: "Our personal touch in grain washing, stone extraction, and hygienic packaging ensures every bag delivered is pure and spotless.",
    qual_link: "Contact Details →",

    // Products Page
    catalog_title: "Product Catalog",
    lbl_pkg: "Package Size",
    lbl_qty: "Quantity",
    btn_add_cart: "+ Add to Order Cart",
    btn_remove: "Remove",
    prod_ulezi_badge: "Finger Millet Flour",
    prod_ulezi_desc: "Traditional breakfast energizer, highly recommended for nursing mothers and postpartum recovery.",
    prod_sooji_badge: "Wheat Grain Flour",
    prod_sooji_desc: "Nutritious option for babies aged 4+ months, ideal for cream cakes, macaroni, and desserts.",
    prod_mtama_badge: "Sorghum Flour",
    prod_mtama_desc: "Great for stiff porridge and baking, beneficial for diabetic diets and fracture recovery.",
    prod_uwele_badge: "Pearl Millet Flour",
    prod_uwele_desc: "Rich in essential minerals and vitamins, specially milled for smooth, wholesome porridge.",
    prod_atta_badge: "WholeMeal Flour",
    prod_atta_desc: "Perfect for wholesome brown bread, pancakes, healthy biscuits, and nutritious daily porridge.",
    prod_sembe_badge: "Maize Flour",
    prod_sembe_desc: "Premium staple food for the whole family, versatile for traditional meals and light pancakes.",
    prod_yellow_badge: "Yellow Corn Meal",
    prod_yellow_desc: "A nutrient-dense yellow corn meal that adds vibrant color and wholesome flavor to your menu.",
    prod_ndizi_badge: "Dried Banana Flour",
    prod_ndizi_desc: "Excellent for traditional soups, banana porridge (mtori/kitawa), or blended with maize meal.",
    prod_wheatgrain_badge: "Semolinna",
    prod_wheatgrain_desc: "Finely graded wheat grain flour crafted for consistent texture and versatile culinary uses.",

    // Order Cart Page
    cart_page_title: "Your Inquiry Cart",
    cart_empty_title: "Your cart is currently empty",
    cart_empty_desc: "Explore our catalog of clean, stone free cereal products and add items to your inquiry.",
    cart_browse_btn: "Browse Products",
    cart_req_title: "Requested Items",
    th_prod: "Product Name",
    th_pkg: "Package Size",
    th_qty: "Quantity",
    th_act: "Action",
    cart_total_lbl: "Total Cartons:",
    form_title: "Customer Details & Submission",
    form_desc: "Fill in your details below to submit your inquiry directly to our sales team via WhatsApp.",
    lbl_name: "Full Name",
    lbl_phone: "Phone Number",
    lbl_loc: "Delivery Location / City",
    btn_submit_wa: "💬 Submit Inquiry via WhatsApp",

    // Mission Page
    mission_page_title: "Our Mission & Core Values",
    mission_core_title: "Our Core Mission",
    mission_core_desc: "To process, mill, and package premium, stone-free cereal flours that provide wholesome nutrition to families across Tanzania and beyond, while maintaining uncompromising standards of hygiene and customer trust.",
    mission_vision_title: "Our Vision",
    mission_vision_desc: "To be the leading household milling enterprise recognized for excellence in family health, infant nutrition, and sustainable agricultural processing.",
    mission_family_title: "Family & Baby Nutrition Commitment",
    mission_family_desc: "Since our establishment in 1981, we have prioritized the health of nursing mothers and growing children. Our specialized flours like Finger Millet and Sorghum are carefully washed and milled to ensure safe, digestible, and nutrient-dense meals for every stage of life.",

    // About Us Page
    about_page_title: "About Nyirefami Limited",
    about_heritage_title: "Our Heritage",
    about_heritage_desc: "Nyirefami Limited was established in 1981 as re-pack flour and other food stuffs. The company ownership is basically family enterprise with proprietorship registration of 1996 with certificate of registration from the Ministry of Industries and Trade No.50656.",
    about_keys_title: "Keys to Success",
    about_keys_desc: "Our competitive advantage lies in our careful personal touch in raw grain preparation, washing, stone extraction, and processing clean flours for local and export distribution.",
    about_contact_title: "Contact Information & Location",
    about_hq: "Nyirefami Limited Headquarters",
    about_tel: "Telephone:",
    about_mob: "Mobile:",
    about_email: "Email:",
    about_wa_title: "Have a Question or Special Inquiry?",
    about_wa_desc: "Whether you need custom grain quantities, wholesale partnerships, or product details, our team is ready to help you directly on WhatsApp.",
    about_wa_btn: "Chat with Us on WhatsApp",

    // Dynamic WhatsApp Greeting
    wa_greeting: "Hello Nyirefami Team. I have a question regarding your products."
  },
  sw: {
    nav_home: "Nyumbani",
    nav_products: "Bidhaa",
    nav_cart: "Kikapu cha Oda",
    nav_mission: "Dhamira",
    nav_about: "Kuhusu Sisi",
    footer_orders: "Oda za Moja kwa Moja:",

    // Home Page
    hero_title: "Halisi na Asilia",
    hero_sub: "Usagaji na ufungashaji bora wa unga safi wa nafaka usio na mawe jijini Arusha, Tanzania.",
    hero_btn: "Tazama na Agiza Bidhaa",
    purpose_badge: "Lengo Letu",
    purpose_title: "Kulisha Familia Nzima kwa Lishe Bora",
    purpose_desc: "Nyirefami Limited imejikita katika kusaga na kufungasha unga bora wa nafaka kwa familia za vipato vyote, kwa msisitizo maalum kwenye lishe ya watoto na familia.",
    purpose_link: "Soma Dhamira Yetu →",
    essential_title: "Bidhaa Zetu Kuu",
    ess_ulezi_desc: "Unga wenye madini ya kalisiamu na chuma kwa wingi, chaguo bora kwa uji wenye nguvu na akina mama wanaonyonyesha.",
    ess_mtama_desc: "Uliosagwa kwa ustadi kutoka nafaka bora za ndani, wenye nyuzinyuzi nyingi kwa ugali na uokaji.",
    ess_sooji_desc: "Unga bora wa ngano unaofaa kwa watoto kuanzia miezi 4+, keki laini, na vyakula mbalimbali.",
    btn_order_ulezi: "Agiza Unga wa Ulezi",
    btn_order_mtama: "Agiza Unga wa Mtama",
    btn_order_sooji: "Agiza Unga wa Sooji",
    exec_title: "Uongozi Mkuu na Menejimenti",
    role_founder: "Mwanzilishi",
    role_ceo: "Mkurugenzi Mtendaji Mkuu (CEO)",
    role_md: "Mkurugenzi Mkuu",
    role_od: "Mkurugenzi wa Uendeshaji",
    hist_title: "Ilianzishwa Jijini Arusha",
    hist_desc: "Ilianzishwa mwaka 1981 ikijishughulisha na usagaji na ufungashaji wa unga na kusajiliwa mwaka 1991 chini ya Cheti Na. 50656, tuna uzoefu wa zaidi ya miongo minne katika usagaji wa nafaka safi.",
    hist_link: "Soma Historia Kamili →",
    qual_title: "Ahadi ya Ubora na Usafi",
    qual_desc: "Uangalizi wetu wa karibu katika kuosha nafaka, kuondoa mawe, na ufungashaji wa usafi unahakikisha kila mfuko unaokufikia ni safi kabisa.",
    qual_link: "Mawasiliano Yetu →",

    // Products Page
    catalog_title: "Orodha ya Bidhaa",
    lbl_pkg: "Ujazo",
    lbl_qty: "Idadi",
    btn_add_cart: "+ Weka Kwenye Kikapu",
    btn_remove: "Ondoa",
    prod_ulezi_badge: "Unga wa Ulezi",
    prod_ulezi_desc: "Kinywaji cha asili chenye kuongeza nguvu asubuhi, kinapendekezwa sana kwa akina mama wanaonyonyesha.",
    prod_sooji_badge: "Unga wa Sooji",
    prod_sooji_desc: "Chaguo lenye lishe kwa watoto wa miezi 4 na kuendelea, unafaa kwa keki, makaroni, na vitafunwa.",
    prod_mtama_badge: "Unga wa Mtama",
    prod_mtama_desc: "Bora kwa ugali, uji, na keki, unafaa pia kwa wenye kisukari na kuwaponyesha haraka wenye majeraha ya mifupa.",
    prod_uwele_badge: "Unga wa Uwele",
    prod_uwele_desc: "Umejaa madini na vitamini muhimu, umesagwa maalum kwa ajili ya uji laini na wenye afya.",
    prod_atta_badge: "Unga wa Atta",
    prod_atta_desc: "Hutumika kwa watu wazima na watoto, Bora kwa kutengeneza mikate (kahawia), chapati, keki, uji  na vyakula vingi vikavu.",
    prod_sembe_badge: "Unga wa Sembe",
    prod_sembe_desc: "Chakula kikuu bora kwa familia nzima, unafaa kwa ugali na mapishi mbalimbali ya nyumbani.",
    prod_yellow_badge: "Sembe ya Njano",
    prod_yellow_desc: "Unga wa mahindi ya njano wenye virutubisho vingi unaoongeza ladha na mvuto kwenye mlo wako.",
    prod_ndizi_badge: "Unga wa Ndizi",
    prod_ndizi_desc: "Bora kwa supu za asili, uji wa ndizi (mtori/kitawa), au kuchanganya na unga wa sembe.",
    prod_wheatgrain_badge: "Semolina",
    prod_wheatgrain_desc: "Unga wa ngano uliopangwa kwa daraja la juu kwa matumizi mbalimbali ya mapishi.",

    // Order Cart Page
    cart_page_title: "Kikapu Chako cha Oda",
    cart_empty_title: "Kikapu chako hakina bidhaa kwa sasa",
    cart_empty_desc: "Tazama orodha yetu ya unga safi wa nafaka usio na mawe na uweke bidhaa kwenye kikapu chako.",
    cart_browse_btn: "Tazama Bidhaa",
    cart_req_title: "Bidhaa Ulizochagua",
    th_prod: "Jina la Bidhaa",
    th_pkg: "Ujazo",
    th_qty: "Idadi",
    th_act: "Hatua",
    cart_total_lbl: "Jumla ya Katoni:",
    form_title: "Taarifa za Mteja na Kutuma Oda",
    form_desc: "Jaza taarifa zako hapa chini ili kutuma oda yako moja kwa moja kwa timu yetu ya mauzo kupitia WhatsApp.",
    lbl_name: "Jina Kamili",
    lbl_phone: "Namba ya Simu",
    lbl_loc: "Mahali pa Kuletewa / Mji",
    btn_submit_wa: "💬 Tuma Oda Kupitia WhatsApp",

    // Mission Page
    mission_page_title: "Dhamira na Maadili Yetu",
    mission_core_title: "Dhamira Yetu Kuu",
    mission_core_desc: "Kusindika, kusaga, na kufungasha unga bora wa nafaka usio na mawe unaotoa lishe kamili kwa familia kote Tanzania na nje ya nchi, huku tukizingatia viwango vya juu vya usafi na uaminifu kwa wateja.",
    mission_vision_title: "Maono Yetu",
    mission_vision_desc: "Kuwa kampuni inayoongoza katika usagaji wa nafaka za nyumbani inayotambulika kwa ubora wa afya ya familia, lishe ya watoto, na usindikaji endelevu wa mazao.",
    mission_family_title: "Ahadi ya Lishe ya Familia na Watoto",
    mission_family_desc: "Tangu kuanzishwa kwetu mwaka 1981, tumeweka kipaumbele afya ya akina mama wanaonyonyesha na watoto wanaokua. Unga wetu maalum kama Ulezi na Mtama huoshwa na kusagwa kwa uangalifu ili kuhakikisha milo salama, rahisi kumeng'enywa, na yenye virutubisho vingi katika kila hatua ya maisha.",

    // About Us Page
    about_page_title: "Kuhusu Nyirefami Limited",
    about_heritage_title: "Historia Yetu",
    about_heritage_desc: "Nyirefami Limited ilianzishwa mwaka 1981 ikijishughulisha na usagaji na ufungashaji wa unga na vyakula vingine. Umiliki wa kampuni ni biashara ya familia iliyosajiliwa mwaka 1996 kwa cheti cha usajili kutoka Wizara ya Viwanda na Biashara Na. 50656.",
    about_keys_title: "Siri ya Mafanikio Yetu",
    about_keys_desc: "Upekee wetu upo katika umakini wetu binafsi wa kuandaa nafaka, kuziosha, kuondoa mawe, na kusaga unga safi kwa ajili ya soko la ndani na nje ya nchi.",
    about_contact_title: "Mawasiliano na Mahali Tulipo",
    about_hq: "Makao Makuu ya Nyirefami Limited",
    about_tel: "Simu ya Mezani:",
    about_mob: "Simu ya Mkononi:",
    about_email: "Barua Pepe:",
    about_wa_title: "Una Swali au Hitaji Maalum?",
    about_wa_desc: "Iwe unahitaji kiasi kikubwa cha nafaka, ushirikiano wa kibiashara, au maelezo ya bidhaa, timu yetu iko tayari kukuhudumia moja kwa moja kupitia WhatsApp.",
    about_wa_btn: "Zungumza Nasi Kupitia WhatsApp",

    // Dynamic WhatsApp Greeting
    wa_greeting: "Habari za Nyirefami Team. Nina swali kuhusu bidhaa zenu."
  }
};

// Active Products (3 Approved Items)
const PRODUCTS = [
  { id: 'ulezi', name: 'Finger Millet Flour', nameSw: 'Unga wa Ulezi', packages: [{ size: '1kg Bag' }, { size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'sooji', name: 'Wheat Grain Flour', nameSw: 'Unga wa Ngano (Sooji)', packages: [{ size: '1kg Bag' }, { size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'mtama', name: 'Sorghum Flour', nameSw: 'Unga wa Mtama', packages: [{ size: '1kg Bag' }, { size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] }
];

// Parked for Future Activation Once Approved
const PENDING_PRODUCTS = [
  { id: 'uwele', name: 'Pearl Millet Flour', nameSw: 'Unga wa Uwele', packages: [{ size: '1kg Bag' }, { size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'atta', name: 'Atta', nameSw: 'Unga wa Atta', packages: [{ size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'sembe', name: 'Maize Flour', nameSw: 'Unga wa Sembe', packages: [{ size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'yellow', name: 'Yellow Corn Meal', nameSw: 'Sembe ya Njano', packages: [{ size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'ndizi', name: 'Dried Banana Flour', nameSw: 'Unga wa Ndizi', packages: [{ size: '1kg Pack' }, { size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] },
  { id: 'wheatgrain', name: 'Semolinna', nameSw: 'Semolina', packages: [{ size: '1kg Bag' }, { size: '2kg Bag' }, { size: '5kg Bag' }, { size: '10kg Bag' }] }
];

function getCurrentLang() {
  return localStorage.getItem('nyirefami_lang') || 'en';
}

function changeLanguage(lang) {
  localStorage.setItem('nyirefami_lang', lang);
  applyTranslations(lang);
  renderCartTable();
}

function applyTranslations(lang) {
  const selectedLang = lang || getCurrentLang();
  const switcher = document.getElementById('lang-switcher');
  if (switcher) switcher.value = selectedLang;

  const elements = document.querySelectorAll('[data-i18n]');
  elements.forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (TRANSLATIONS[selectedLang] && TRANSLATIONS[selectedLang][key]) {
      el.textContent = TRANSLATIONS[selectedLang][key];
    }
  });

  // Dynamically update WhatsApp chat button greeting based on selected language
  const waBtn = document.getElementById('whatsapp-chat-btn');
  if (waBtn && TRANSLATIONS[selectedLang] && TRANSLATIONS[selectedLang].wa_greeting) {
    const greetingText = TRANSLATIONS[selectedLang].wa_greeting;
    waBtn.href = `https://wa.me/255754287707?text=${encodeURIComponent(greetingText)}`;
  }
}

function getCart() {
  const rawCart = JSON.parse(localStorage.getItem('nyirefami_cart')) || [];
  const allowedIds = PRODUCTS.map(p => p.id);
  return rawCart.filter(item => allowedIds.includes(item.id));
}

function saveCart(cart) {
  localStorage.setItem('nyirefami_cart', JSON.stringify(cart));
  updateCartBadge();
}

function updateCartBadge() {
  const cart = getCart();
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  const badgeEl = document.getElementById('cart-badge');
  if (badgeEl) {
    badgeEl.textContent = totalItems;
  }
}

function addToCart(productId, packageIndexStr, qtyInputId) {
  const product = PRODUCTS.find(p => p.id === productId);
  if (!product) return;

  const packageIndex = parseInt(packageIndexStr, 10);
  const pkg = product.packages[packageIndex];
  const qtyInput = document.getElementById(qtyInputId);
  const quantity = parseInt(qtyInput ? qtyInput.value : 1, 10) || 1;

  let cart = getCart();
  const existingIndex = cart.findIndex(item => item.id === productId && item.packageSize === pkg.size);

  if (existingIndex > -1) {
    cart[existingIndex].quantity += quantity;
  } else {
    cart.push({
      id: productId,
      name: product.name,
      nameSw: product.nameSw,
      packageSize: pkg.size,
      quantity: quantity
    });
  }

  saveCart(cart);

  const lang = getCurrentLang();
  const displayName = lang === 'sw' ? product.nameSw : product.name;
  const msg = lang === 'sw'
    ? `Umeongeza ${quantity}x ${displayName} (${pkg.size}) kwenye kikapu chako!`
    : `Successfully added ${quantity}x ${displayName} (${pkg.size}) to your inquiry cart!`;

  showToast(msg);
}

function showToast(message) {
  let toast = document.getElementById('toast-notification');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'toast-notification';
    document.body.appendChild(toast);
  }

  toast.textContent = message;
  toast.classList.add('show');

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

function removeFromCart(index) {
  let cart = getCart();
  cart.splice(index, 1);
  saveCart(cart);
  renderCartTable();
}

function renderCartTable() {
  const cartTableBody = document.getElementById('cart-items-body');
  const mobileCartList = document.getElementById('mobile-cart-items');
  const totalPackagesEl = document.getElementById('cart-total-packages');
  const emptyCartMsg = document.getElementById('empty-cart-msg');
  const cartContentSection = document.getElementById('cart-content-section');

  if (!cartTableBody && !mobileCartList) return;

  const cart = getCart();
  const lang = getCurrentLang();
  const removeLabel = TRANSLATIONS[lang]?.btn_remove || "Remove";

  if (cart.length === 0) {
    if (emptyCartMsg) emptyCartMsg.style.display = 'block';
    if (cartContentSection) cartContentSection.style.display = 'none';
    if (cartTableBody) cartTableBody.innerHTML = '';
    if (mobileCartList) mobileCartList.innerHTML = '';
    if (totalPackagesEl) totalPackagesEl.textContent = '0';
    return;
  }

  if (emptyCartMsg) emptyCartMsg.style.display = 'none';
  if (cartContentSection) cartContentSection.style.display = 'grid';

  let totalPackages = 0;

  if (cartTableBody) {
    cartTableBody.innerHTML = cart.map((item, index) => {
      const quantity = Number(item.quantity) || 1;
      totalPackages += quantity;
      const itemName = (lang === 'sw' && item.nameSw) ? item.nameSw : item.name;

      return `
        <tr>
          <td><strong>${itemName}</strong></td>
          <td>${item.packageSize}</td>
          <td>${quantity}</td>
          <td><button class="btn-remove" onclick="removeFromCart(${index})">${removeLabel}</button></td>
        </tr>
      `;
    }).join('');
  }

  if (mobileCartList) {
    mobileCartList.innerHTML = cart.map((item, index) => {
      const quantity = Number(item.quantity) || 1;
      const itemName = (lang === 'sw' && item.nameSw) ? item.nameSw : item.name;
      if (!cartTableBody) totalPackages += quantity;

      return `
        <div class="mobile-cart-item">
          <div class="mobile-cart-info">
            <h4>${itemName}</h4>
            <p>${item.packageSize} &bull; ${lang === 'sw' ? 'Idadi' : 'Qty'}: ${quantity}</p>
          </div>
          <div class="mobile-cart-pricing">
            <button class="btn-remove" style="margin-top: 6px; padding: 4px 10px; font-size: 0.75rem;" onclick="removeFromCart(${index})">${removeLabel}</button>
          </div>
        </div>
      `;
    }).join('');
  }

  if (totalPackagesEl) {
    totalPackagesEl.textContent = totalPackages;
  }
}

// Process Final Inquiry / Order Request (Matches Selected Language)
function submitOrder(e) {
  e.preventDefault();
  const cart = getCart();
  const lang = getCurrentLang();

  if (cart.length === 0) {
    showToast(lang === 'sw' ? "Kikapu chako hakina bidhaa. Tafadhali chagua bidhaa kwanza." : "Your cart is empty. Please add products first.");
    return;
  }

  const name = document.getElementById('cust-name').value;
  const phone = document.getElementById('cust-phone').value;
  const location = document.getElementById('cust-location').value;

  let orderSummary = "";

  if (lang === 'sw') {
    orderSummary = `ODA MPYA YA BIDHAA - NYIREFAMI LIMITED\n\n`;
    orderSummary += `Mteja: ${name}\n`;
    orderSummary += `Namba ya Simu: ${phone}\n`;
    orderSummary += `Mahali pa Kuletewa: ${location}\n`;
    orderSummary += `\nBIDHAA ZILIZOOMBIWA:\n`;

    let totalPackages = 0;
    cart.forEach((item, i) => {
      const qty = Number(item.quantity) || 1;
      totalPackages += qty;
      const itemName = item.nameSw || item.name;
      orderSummary += `${i + 1}. ${itemName} (${item.packageSize}) - Idadi: ${qty}\n`;
    });

    orderSummary += `\nJumla ya Katoni: ${totalPackages}\n\nTafadhali thibitisha bei na upatikanaji. Asante!`;
  } else {
    orderSummary = `NEW PRODUCT INQUIRY - NYIREFAMI LIMITED\n\n`;
    orderSummary += `Customer: ${name}\n`;
    orderSummary += `Phone: ${phone}\n`;
    orderSummary += `Delivery Location: ${location}\n`;
    orderSummary += `\nREQUESTED ITEMS:\n`;

    let totalPackages = 0;
    cart.forEach((item, i) => {
      const qty = Number(item.quantity) || 1;
      totalPackages += qty;
      orderSummary += `${i + 1}. ${item.name} (${item.packageSize}) - Qty: ${qty}\n`;
    });

    orderSummary += `\nTotal Cartons: ${totalPackages}\n\nPlease confirm price quotation and availability. Thank you!`;
  }

  const whatsappUrl = `https://wa.me/255754278149?text=${encodeURIComponent(orderSummary)}`;
  
  localStorage.removeItem('nyirefami_cart');
  updateCartBadge();

  window.open(whatsappUrl, '_blank');
  window.location.href = 'index.html';
}

window.addEventListener('scroll', function() {
  const header = document.querySelector('header');
  if (header) {
    if (window.scrollY > 40) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  }
});

document.addEventListener('DOMContentLoaded', function() {
  updateCartBadge();
  renderCartTable();
  applyTranslations(getCurrentLang());
});