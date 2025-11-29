    <?php
    if (!isset($baseUrl)) {
        if (!function_exists('getBaseUrl')) {
            require_once __DIR__ . '/../../utils/session.php';
        }
        $baseUrl = getBaseUrl();
    }
    $isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    if ($isAdminPage && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin'):
    ?>
            </div>
        </div>
    <?php endif; ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="main-content">
            <div class="row">
                <!-- Column 1 -->
                <div class="column">
                    <img
                        src="<?php echo $baseUrl; ?>/assets/img/logo.svg"
                        alt="Logo"
                        class="logo"
                        style="height: 40px;"
                    />
                    <p class="desc">
                        Cửa hàng văn phòng phẩm chất lượng cao, đáp ứng mọi nhu cầu văn phòng của bạn.
                    </p>
                    <div class="socials">
                        <a href="#!">
                            <img
                                src="<?php echo $baseUrl; ?>/assets/icons/twitter.svg"
                                alt="Twitter"
                                class="icon"
                            />
                        </a>
                        <a href="#!">
                            <img
                                src="<?php echo $baseUrl; ?>/assets/icons/facebook.svg"
                                alt="Facebook"
                                class="icon"
                            />
                        </a>
                        <a href="#!">
                            <img
                                src="<?php echo $baseUrl; ?>/assets/icons/linkedin.svg"
                                alt="Linkedin"
                                class="icon"
                            />
                        </a>
                        <a href="#!">
                            <img
                                src="<?php echo $baseUrl; ?>/assets/icons/instagram.svg"
                                alt="Instagram"
                                class="icon"
                            />
                        </a>
                    </div>
                </div>
                
                <!-- Column 2 -->
                <div class="column">
                    <h3 class="title">Công ty</h3>
                    <ul class="list">
                        <li>
                            <a href="#!">Về chúng tôi</a>
                        </li>
                        <li>
                            <a href="#!">Tính năng</a>
                        </li>
                        <li>
                            <a href="#!">Bảng giá</a>
                        </li>
                        <li>
                            <a href="#!">Tin tức mới nhất</a>
                        </li>
                    </ul>
                </div>
                
                <!-- Column 3 -->
                <div class="column">
                    <h3 class="title">Hỗ trợ</h3>
                    <ul class="list">
                        <li>
                            <a href="#!">Câu hỏi thường gặp</a>
                        </li>
                        <li>
                            <a href="#!">Điều khoản & Điều kiện</a>
                        </li>
                        <li>
                            <a href="#!">Chính sách bảo mật</a>
                        </li>
                        <li>
                            <a href="#!">Liên hệ</a>
                        </li>
                    </ul>
                </div>
                
                <!-- Column 4 -->
                <div class="column">
                    <h3 class="title">Địa chỉ</h3>
                    <ul class="list">
                        <li>
                            <a href="#!">
                                <strong>Địa chỉ:</strong> Việt Nam
                            </a>
                        </li>
                        <li>
                            <a href="#!">
                                <strong>Email:</strong> info@example.com
                            </a>
                        </li>
                        <li>
                            <a href="#!">
                                <strong>Điện thoại:</strong> +84 123 456 789
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 Cửa hàng văn phòng phẩm. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

