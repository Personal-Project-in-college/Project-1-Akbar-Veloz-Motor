document.addEventListener('DOMContentLoaded', function () {
    const wishlistContainer = document.querySelector('.wishlist-items');
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

    if (wishlist.length === 0) {
        wishlistContainer.innerHTML = '<p>Belum ada kendaraan yang disukai.</p>';
        return;
    }

    wishlist.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('wishlist-item');

        itemDiv.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="item-info">
                <h3>${item.name}</h3>
                <p>${item.price}</p>
                <div class="item-actions">
                    <a href="detail.html" class="btn-secondary">Lihat Detail</a>
                    <button class="remove-btn" data-name="${item.name}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;

        wishlistContainer.appendChild(itemDiv);
    });

    wishlistContainer.addEventListener('click', function (e) {
        if (e.target.closest('.remove-btn')) {
            const button = e.target.closest('.remove-btn');
            const nameToRemove = button.getAttribute('data-name');

            const updatedWishlist = wishlist.filter(item => item.name !== nameToRemove);
            localStorage.setItem('wishlist', JSON.stringify(updatedWishlist));

            // Tampilkan toast
            showToast(`"${nameToRemove}" dihapus dari wishlist.`);

            // Refresh halaman setelah delay
            setTimeout(() => location.reload(), 1000);
        }
    });
});
