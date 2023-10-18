import emptyDropzone from '@/assets/empty-dropzone.png';

function EmptyDropzone() {
    return (
        <div className='grid h-full place-items-center bg-blue-50'>
            <div className='max-w-xl space-y-6 text-center'>
                <img src={emptyDropzone} className='mx-auto h-72 w-72 ' />
                <h1 className='text-[32px] font-bold text-metalic-blue'>
                    Your form is empty!
                </h1>
                <p className='text-[20px] text-metalic-blue'>
                    Build your form by dragging on texts, numbers, images, and
                    more from the Component menu to your form canvas.
                </p>
            </div>
        </div>
    );
}

export default EmptyDropzone;
