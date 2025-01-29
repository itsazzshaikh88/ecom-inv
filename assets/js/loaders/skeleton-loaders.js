function skeletonContent() {

}

function supplierListSkeleton() {
    return `<tr class="skeleton-loader">
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 offline avatar-rounded">
                                    <div class="skeleton-box" style="width: 30px; height: 20px; border-radius: 50%;"></div>
                                </span>
                                <div class="skeleton-box" style="width: 100px; height: 20px;"></div>
                            </div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 150px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 100px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 80px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 80px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 80px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="hstack gap-2 fs-15">
                                <div class="skeleton-box" style="width: 30px; height: 30px; border-radius: 4px;"></div>
                                <div class="skeleton-box" style="width: 30px; height: 30px; border-radius: 4px;"></div>
                            </div>
                        </td>
            </tr>`;
}

function usersListSkeleton() {
    return `<tr class="skeleton-loader">
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 offline avatar-rounded">
                                    <div class="skeleton-box" style="width: 30px; height: 20px; border-radius: 50%;"></div>
                                </span>
                                <div class="skeleton-box" style="width: 100px; height: 20px;"></div>
                            </div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 150px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 100px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 80px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 80px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="skeleton-box" style="width: 80px; height: 20px;"></div>
                        </td>
                        <td>
                            <div class="hstack gap-2 fs-15">
                                <div class="skeleton-box" style="width: 30px; height: 30px; border-radius: 4px;"></div>
                                <div class="skeleton-box" style="width: 30px; height: 30px; border-radius: 4px;"></div>
                            </div>
                        </td>
            </tr>`;
}

function commonSkeletonContent(columns) {
    content = `<tr>`;
    for (let i = 0; i < columns; i++) {
        content += `<td>
                        <div class="skeleton-box" style="width: 100%; height: 20px;"></div>
                    </td>`;
    }
    content += `</tr>`;
    return content;
}

