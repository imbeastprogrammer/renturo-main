import { toast } from 'react-hot-toast';
import Success from '../components/toast/super-admin/Success';
import Error from '../components/toast/super-admin/Error';

type Params = { title?: string; description?: string };

function useCentralToast() {
    const success = (params?: Params) =>
        toast.custom((t) =>
            t.visible ? (
                <Success {...params} onClose={() => toast.dismiss(t.id)} />
            ) : null,
        );
    const error = (params?: Params) =>
        toast.custom((t) =>
            t.visible ? (
                <Error {...params} onClose={() => toast.dismiss(t.id)} />
            ) : null,
        );

    return { success, error };
}

export default useCentralToast;
