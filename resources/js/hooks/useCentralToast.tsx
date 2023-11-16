import { toast } from 'react-hot-toast';
import Success from '../components/toast/super-admin/Success';
import Error from '../components/toast/super-admin/Error';

type Params = { title?: string; description?: string };

function useCentralToast() {
    const success = (params?: Params) => toast.custom(<Success {...params} />);
    const error = (params?: Params) => toast.custom(<Error {...params} />);

    return { success, error };
}

export default useCentralToast;
