import { toast } from 'react-hot-toast';
import Success from '../components/toast/owner/Success';
import Error from '../components/toast/owner/Error';

type Params = { title?: string; description?: string };

function useOwnerToast() {
    const success = (params?: Params) => toast.custom(<Success {...params} />);
    const error = (params?: Params) => toast.custom(<Error {...params} />);

    return { success, error };
}

export default useOwnerToast;
