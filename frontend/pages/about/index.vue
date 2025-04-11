<template>
    <main class="bg-gray-50">
        <!-- Hero Section -->
        <section class="hero min-h-[60vh] relative" style="background-image: url('/images/hero.png');">
            <div class="hero-overlay opacity-90 bg-green-900"></div>
            <div class="hero-content text-center text-white py-16">
                <div class="max-w-3xl">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">HTX Nông Nghiệp Dịch Vụ Tương Lai Xanh</h1>
                    <p class="text-xl mb-8">An toàn người trồng – Sức khỏe người dùng</p>
                    <a href="#story" class="btn btn-success text-white px-8">Tìm hiểu thêm</a>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section id="story" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold text-green-800 text-center mb-10">Câu chuyện của chúng tôi</h2>
                    <div class="prose prose-lg max-w-none">
                        <p>
                            HTX Nông Nghiệp Dịch Vụ Tương Lai Xanh là một cộng đồng hợp tác được thành lập với mục tiêu
                            phát triển nền nông nghiệp hữu cơ bền vững tại Bạc Liêu, Việt Nam. Chúng tôi là nơi kết nối
                            những người nông dân tâm huyết và các chuyên gia trong ngành để cùng tạo ra một nền nông
                            nghiệp xanh, sạch và an toàn cho sức khỏe cộng đồng.
                        </p>
                        <p>
                            Với phương châm <strong>"An toàn người trồng – Sức khỏe người dùng"</strong>, chúng tôi cam
                            kết mang lại những sản phẩm nông nghiệp chất lượng cao, đảm bảo an toàn cho cả người sản
                            xuất và người tiêu dùng.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5 Principles Section -->
        <section class="py-16 bg-green-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-green-800 text-center mb-6">Nguyên tắc 5 Không</h2>
                <p class="text-center text-lg mb-12 max-w-3xl mx-auto">
                    Vườn rau HTX Tương lai xanh cam kết sản xuất theo nguyên tắc "5 Không" để đảm bảo sản phẩm luôn an
                    toàn và lành mạnh.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="card card-lg bg-white shadow-md hover:shadow-lg transition-shadow">
                        <div class="card-body">
                            <div class="flex items-center mb-4">
                                <X
                                    class="w-12 h-12 bg-green-600 btn rounded-full flex items-center justify-center text-white p-3 mr-4" />
                                <h3 class="card-title text-green-800">Không thuốc trừ sâu & diệt cỏ</h3>
                            </div>
                            <p>Chúng tôi không sử dụng bất kỳ loại thuốc trừ sâu hay thuốc diệt cỏ hóa học nào trong quá
                                trình canh tác.</p>
                        </div>
                    </div>

                    <div class="card card-lg bg-white shadow-md hover:shadow-lg transition-shadow">
                        <div class="card-body">
                            <div class="flex items-center mb-4">
                                <X
                                    class="w-12 h-12 bg-green-600 btn rounded-full flex items-center justify-center text-white p-3 mr-4" />
                                <h3 class="card-title text-green-800">Không kích thích tăng trưởng</h3>
                            </div>
                            <p>Rau củ được phát triển tự nhiên, không sử dụng bất kỳ loại thuốc kích thích tăng trưởng
                                nào.</p>
                        </div>
                    </div>

                    <div class="card card-lg bg-white shadow-md hover:shadow-lg transition-shadow">
                        <div class="card-body">
                            <div class="flex items-center mb-4">
                                <X
                                    class="w-12 h-12 bg-green-600 btn rounded-full flex items-center justify-center text-white p-3 mr-4" />
                                <h3 class="card-title text-green-800">Không phân bón hóa học</h3>
                            </div>
                            <p>Chúng tôi chỉ sử dụng phân bón hữu cơ như OM 25%, đất sạch Vì Nông và đạm cá tự nhiên.
                            </p>
                        </div>
                    </div>

                    <div class="card card-lg bg-white shadow-md hover:shadow-lg transition-shadow">
                        <div class="card-body">
                            <div class="flex items-center mb-4">
                                <X
                                    class="w-12 h-12 bg-green-600 btn rounded-full flex items-center justify-center text-white p-3 mr-4" />
                                <h3 class="card-title text-green-800">Không giống biến đổi gen</h3>
                            </div>
                            <p>Chỉ sử dụng hạt giống tự nhiên, không biến đổi gen để đảm bảo tính nguyên bản và an toàn.
                            </p>
                        </div>
                    </div>

                    <div class="card card-lg bg-white shadow-md hover:shadow-lg transition-shadow">
                        <div class="card-body">
                            <div class="flex items-center mb-4">
                                <X
                                    class="w-12 h-12 bg-green-600 btn rounded-full flex items-center justify-center text-white p-3 mr-4" />
                                <h3 class="card-title text-green-800">Không đất và nước nhiễm hóa chất</h3>
                            </div>
                            <p>Canh tác trên đất sạch, sử dụng nguồn nước không nhiễm hóa chất nông nghiệp độc hại.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Process Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-green-800 text-center mb-10">Quy trình canh tác</h2>

                <div class="flex flex-col md:flex-row flex-wrap justify-center gap-8 max-w-5xl mx-auto">
                    <div
                        class="card card-lg bg-green-50 shadow-md hover:shadow-lg transition-shadow w-full md:w-[calc(50%-1rem)]">
                        <div class="card-body">
                            <h3 class="card-title text-green-800">Giống và đất trồng</h3>
                            <ul class="list-disc list-inside space-y-2 mt-2">
                                <li>Giống: Sử dụng giống chất lượng cao từ công ty Rạng Đông</li>
                                <li>Đất trồng: Đất sạch Vì Nông, được xử lý hữu cơ</li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="card card-lg bg-green-50 shadow-md hover:shadow-lg transition-shadow w-full md:w-[calc(50%-1rem)]">
                        <div class="card-body">
                            <h3 class="card-title text-green-800">Phân bón</h3>
                            <ul class="list-disc list-inside space-y-2 mt-2">
                                <li>Phân hữu cơ OM 25%</li>
                                <li>Đạm cá tự nhiên</li>
                                <li>Hoàn toàn không sử dụng phân bón hóa học</li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="card card-lg bg-green-50 shadow-md hover:shadow-lg transition-shadow w-full md:w-[calc(50%-1rem)]">
                        <div class="card-body">
                            <h3 class="card-title text-green-800">Bảo vệ thực vật</h3>
                            <ul class="list-disc list-inside space-y-2 mt-2">
                                <li>Sử dụng nấm xanh và nấm 3 màu</li>
                                <li>Dầu neem tự nhiên</li>
                                <li>Chiết xuất từ gừng, tỏi, ớt</li>
                                <li>Không sử dụng thuốc trừ sâu hóa học</li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="card card-lg bg-green-50 shadow-md hover:shadow-lg transition-shadow w-full md:w-[calc(50%-1rem)]">
                        <div class="card-body">
                            <h3 class="card-title text-green-800">Thu hoạch</h3>
                            <ul class="list-disc list-inside space-y-2 mt-2">
                                <li>Thu hoạch đúng thời điểm để đảm bảo chất lượng tốt nhất</li>
                                <li>Vận chuyển nhanh chóng đến tay người tiêu dùng</li>
                                <li>Đóng gói cẩn thận, đảm bảo độ tươi ngon</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Location Section -->
        <!-- <section class="pb-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold text-green-800 text-center mb-10">Địa điểm sản xuất</h2>
                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        <div class="w-full md:w-1/2">
                            <div class="aspect-video rounded-lg overflow-hidden shadow-lg">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.5746862204485!2d105.76892191478396!3d9.246656793373337!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwMTQnNDcuOSJOIDEwNcKwNDYnMTguMiJF!5e0!3m2!1svi!2s!4v1553181095500!"
                                    width="100%" height="100%" style="border:0;" loading="lazy">
                                </iframe>
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <div class="prose prose-lg">
                                <h3 class="text-xl font-semibold text-green-800">Hộ anh Điệp</h3>
                                <p><strong>Địa chỉ:</strong> Vĩnh Trạch Đông, thành phố Bạc Liêu, tỉnh Bạc Liêu</p>
                                <p><strong>Tọa độ:</strong> 9°14'47.9"N 105°46'18.2"E</p>
                                <p>
                                    Vùng trồng của chúng tôi nằm tại khu vực có môi trường sinh thái lý tưởng cho việc
                                    canh tác hữu cơ,
                                    với nguồn nước sạch và đất màu mỡ, không bị ô nhiễm bởi hóa chất nông nghiệp.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- Call to Action -->
        <section class="py-16 bg-green-600 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold mb-6">Liên hệ với chúng tôi</h2>
                <p class="max-w-2xl mx-auto text-lg mb-8">
                    Chúng tôi luôn sẵn sàng cung cấp các sản phẩm nông nghiệp hữu cơ, an toàn và chất lượng cao.
                    Đăng ký ngay để được cập nhật những thông tin mới nhất về sản phẩm và mùa vụ sắp tới!
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <NuxtLink to="/products" class="btn btn-lg bg-white text-green-600 hover:bg-gray-100 border-none">
                        Đăng ký mua sản phẩm
                    </NuxtLink>
                    <NuxtLink to="https://youtu.be/xIhtZiAZ0zE"
                        class="btn btn-lg btn-outline border-white text-white hover:bg-white hover:text-green-600">
                        Xem video giới thiệu
                    </NuxtLink>
                </div>
            </div>
        </section>
    </main>
</template>

<script setup lang="ts">
import { X } from 'lucide-vue-next';

// Có thể thêm script nếu cần
</script>