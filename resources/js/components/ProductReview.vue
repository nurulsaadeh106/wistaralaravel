# ProductReview.vue
<template>
  <div class="reviews-section bg-white p-6 rounded-lg shadow">
    <!-- Review Form -->
    <div v-if="isLoggedIn" class="review-form mb-8">
      <h3 class="text-xl font-semibold mb-4">Tulis Review</h3>
      <div class="space-y-4">
        <div>
          <label class="block text-gray-700 mb-2">Rating</label>
          <div class="flex space-x-2">
            <button 
              v-for="star in 5" 
              :key="star"
              @click="newReview.rating = star"
              class="text-2xl focus:outline-none"
              :class="star <= newReview.rating ? 'text-yellow-400' : 'text-gray-300'"
            >
              ★
            </button>
          </div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Komentar</label>
          <textarea 
            v-model="newReview.comment"
            class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            rows="4"
          ></textarea>
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Foto Produk (Opsional)</label>
          <input 
            type="file" 
            @change="handlePhotoUpload" 
            multiple 
            accept="image/*"
            class="w-full p-2 border rounded-lg"
          >
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Video Review (Opsional)</label>
          <input 
            type="file" 
            @change="handleVideoUpload" 
            accept="video/*"
            class="w-full p-2 border rounded-lg"
          >
        </div>

        <button 
          @click="submitReview"
          class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Mengirim...' : 'Kirim Review' }}
        </button>
      </div>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list">
      <h3 class="text-xl font-semibold mb-4">Reviews ({{ reviews.length }})</h3>
      <div v-if="reviews.length === 0" class="text-gray-500">
        Belum ada review untuk produk ini.
      </div>
      <div v-else class="space-y-6">
        <div v-for="review in reviews" :key="review.id" class="review-item border-b pb-6">
          <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                  <span class="text-xl">{{ review.user.name.charAt(0) }}</span>
                </div>
              </div>
              <div>
                <h4 class="font-semibold">{{ review.user.name }}</h4>
                <div class="text-yellow-400">
                  {{ '★'.repeat(review.rating) }}
                  <span class="text-gray-300">{{ '★'.repeat(5 - review.rating) }}</span>
                </div>
                <p class="text-gray-600 mt-2">{{ review.comment }}</p>
              </div>
            </div>
            <span class="text-gray-400 text-sm">
              {{ formatDate(review.created_at) }}
            </span>
          </div>

          <!-- Review Photos -->
          <div v-if="review.photos && review.photos.length" class="mt-4 flex space-x-2 overflow-x-auto">
            <img 
              v-for="(photo, index) in review.photos" 
              :key="index"
              :src="getPhotoUrl(photo)"
              class="w-24 h-24 object-cover rounded"
              @click="openPhotoViewer(review.photos, index)"
            >
          </div>

          <!-- Review Video -->
          <div v-if="review.video" class="mt-4">
            <video 
              :src="getVideoUrl(review.video)" 
              controls
              class="max-w-full h-auto rounded"
            ></video>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
  name: 'ProductReview',
  props: {
    productId: {
      type: Number,
      required: true
    }
  },

  setup(props) {
    const reviews = ref([]);
    const isSubmitting = ref(false);
    const toast = useToast();
    const isLoggedIn = ref(false); // Ganti dengan state management yang sesuai

    const newReview = ref({
      rating: 0,
      comment: '',
      photos: [],
      video: null
    });

    const fetchReviews = async () => {
      try {
        const response = await axios.get(`/api/reviews?product_id=${props.productId}`);
        reviews.value = response.data;
      } catch (error) {
        toast.error('Gagal memuat review');
      }
    };

    const handlePhotoUpload = (event) => {
      newReview.value.photos = Array.from(event.target.files);
    };

    const handleVideoUpload = (event) => {
      newReview.value.video = event.target.files[0];
    };

    const submitReview = async () => {
      if (!newReview.value.rating || !newReview.value.comment) {
        toast.error('Rating dan komentar harus diisi');
        return;
      }

      isSubmitting.value = true;
      const formData = new FormData();
      formData.append('id_produk', props.productId);
      formData.append('rating', newReview.value.rating);
      formData.append('comment', newReview.value.comment);
      
      newReview.value.photos.forEach(photo => {
        formData.append('photos[]', photo);
      });

      if (newReview.value.video) {
        formData.append('video', newReview.value.video);
      }

      try {
        await axios.post('/api/reviews', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        
        toast.success('Review berhasil ditambahkan');
        newReview.value = { rating: 0, comment: '', photos: [], video: null };
        await fetchReviews();
      } catch (error) {
        toast.error('Gagal menambahkan review');
      } finally {
        isSubmitting.value = false;
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const getPhotoUrl = (photo) => {
      return `/storage/${photo}`;
    };

    const getVideoUrl = (video) => {
      return `/storage/${video}`;
    };

    onMounted(() => {
      fetchReviews();
    });

    return {
      reviews,
      newReview,
      isSubmitting,
      isLoggedIn,
      handlePhotoUpload,
      handleVideoUpload,
      submitReview,
      formatDate,
      getPhotoUrl,
      getVideoUrl
    };
  }
};
</script>