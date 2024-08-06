import { ElLoading } from 'element-plus';

export function loader() {
  let loadingInstance = null;

  const startLoading = (text = 'Loading') => {
    loadingInstance = ElLoading.service({
      lock: true,
      text: text,
      background: 'rgba(0, 0, 0, 0.7)',
    });
  };

  const stopLoading = () => {
    if (loadingInstance) {
      loadingInstance.close();
      loadingInstance = null;
    }
  };

  return { startLoading, stopLoading };
}
