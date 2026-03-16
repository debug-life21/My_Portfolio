    </main>
    <footer>
        <p>&copy; 2025 DKU Smart Cafe. <?= __('all_rights') ?></p>
    </footer>
</div>

<!-- Custom Logout Modal -->
<div id="logoutModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3 style="margin-bottom: 15px; color: #333;"><?= __('logout_confirm') ?></h3>
        <div class="modal-buttons">
            <button onclick="window.location.href='logout.php'" class="btn btn-danger" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px;"><?= __('logout') ?></button>
            <button onclick="hideLogoutModal()" class="btn" style="background-color: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; margin-left: 10px;"><?= __('cancel') ?></button>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 300px;
}
.modal-buttons {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
.btn-danger:hover {
    background-color: #c82333 !important;
}
.btn[onclick="hideLogoutModal()"]:hover {
    background-color: #5a6268 !important;
}
</style>

<script>
function showLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}
function hideLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}
</script>

</body>
</html>
