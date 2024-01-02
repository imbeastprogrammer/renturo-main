interface GetMessageParams {
    props: {
        flash: { success: string };
    };
}

const getSuccessMessage = (data: unknown) => {
    const params = data as GetMessageParams;

    return params.props.flash.success;
};

export default getSuccessMessage;
