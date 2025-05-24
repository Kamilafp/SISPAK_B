<footer class="footer-section bg-dark text-white mt-5">
    <style>
        .footer-section {
            background: linear-gradient(135deg, #2c3e50, #34495e) !important;
            font-size: 0.95rem;
        }
        
        .footer-section a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-section a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .footer-section h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 1.2rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-section h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: #3498db;
        }
        
        .footer-section .list-unstyled li {
            margin-bottom: 0.6rem;
        }
        
        .footer-section address {
            color: rgba(255,255,255,0.7);
            font-style: normal;
            line-height: 1.6;
        }
        
        .footer-section address strong {
            color: white;
            font-weight: 600;
        }
        
        .footer-bottom {
            background: rgba(0,0,0,0.2);
            padding: 1rem 0;
            font-size: 0.9rem;
        }
        
        .social-icons {
            margin-top: 1.5rem;
        }
        
        .social-icons a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 8px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: #3498db;
            transform: translateY(-3px);
            padding-left: 0;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .contact-icon {
            margin-right: 10px;
            color: #3498db;
            font-size: 1.1rem;
            margin-top: 3px;
        }
    </style>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h5>Tentang Sistem</h5>
                <p class="mb-3">Sistem pakar berbasis web untuk membantu diagnosa penyakit gigi menggunakan metode Naïve Bayes.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5>Menu</h5>
                <ul class="list-unstyled">
                    <li><a href="/SISPAK_B/index.php"><i class="fas fa-chevron-right mr-2"></i>Home</a></li>
                    <li><a href="/SISPAK_B/konsultasi.php"><i class="fas fa-chevron-right mr-2"></i>Konsultasi</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right mr-2"></i>Tentang</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right mr-2"></i>Kontak</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5>Kontak Kami</h5>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <strong>Universitas Jenderal Soedirman</strong><br>
                        Program Studi Informatika<br>
                        Fakultas Teknik
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>info@sispakgigi.unsoed.ac.id</div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>+62 123 4567 8910</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom text-center py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    &copy; <?= date('Y') ?> Sistem Pakar Diagnosa Penyakit Gigi. All Rights Reserved.
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>